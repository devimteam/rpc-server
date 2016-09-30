<?php

namespace Devim\Component\RpcServer;

use Devim\Component\RpcServer\Exception\RpcInvalidParamsException;
use Devim\Component\RpcServer\Exception\RpcInvalidRequestException;
use Devim\Component\RpcServer\Exception\RpcMethodNotFoundException;
use Devim\Component\RpcServer\Exception\RpcParseException;
use Devim\Component\RpcServer\Exception\RpcServiceExistsException;
use Devim\Component\RpcServer\Exception\RpcServiceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Component\RpcServer
 */
class RpcServer
{
    const JSON_RPC_VERSION = '2.0';

    /**
     * @var array
     */
    private $servicesCache = [];

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var MiddlewareInterface[]
     */
    private $middleware = [];

    /**
     * @param string $name
     * @param \Closure $callback
     *
     * @return $this
     *
     * @throws RpcServiceExistsException
     */
    public function addService(string $className, \Closure $parametersCallback)
    {
        if (isset($this->services[$className])) {
            throw new RpcServiceExistsException($className);
        }

        $classShortName = (new \ReflectionClass($className))->getShortName();
        $name = lcfirst(substr($classShortName, 0, strlen($classShortName) - 10));

        $this->services[$name] = [$className, $parametersCallback];

        return $this;
    }

    /**
     * @return \Closure[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function run(Request $request) : JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        return JsonResponse::create($this->doRun($payload));
    }

    /**
     * @param mixed $payload
     *
     * @return mixed
     */
    private function doRun($payload)
    {
        try {
            $this->validatePayload($payload);

            if ($this->isBatchRequest($payload)) {
                return $this->parseBatchRequest($payload);
            }

            return $this->parseRequest($payload);

        } catch (\Throwable $e) {
            if ($e instanceof RpcInvalidRequestException || $e instanceof RpcParseException) {
                return $this->handleExceptions(null, $e);
            }

            if (!$this->isNotification($payload)) {
                return $this->handleExceptions($payload['id'], $e);
            }
        }
    }

    /**
     * @param mixed $payload
     *
     * @throws RpcInvalidRequestException
     * @throws RpcParseException
     */
    private function validatePayload($payload)
    {
        if (!is_array($payload)) {
            throw new RpcParseException();
        }

        if (!isset($payload['jsonrpc']) ||
            !isset($payload['method']) ||
            !is_string($payload['method']) ||
            $payload['jsonrpc'] !== RpcServer::JSON_RPC_VERSION ||
            (isset($payload['params']) && !is_array($payload['params']))
        ) {
            throw new RpcInvalidRequestException();
        }
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    private function isBatchRequest(array $payload) : bool
    {
        return array_keys($payload) === range(0, count($payload) - 1);
    }

    /**
     * @param array $payloads
     *
     * @return array
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcServiceNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcParseException
     * @throws \Devim\Component\RpcServer\Exception\RpcMethodNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidRequestException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     */
    private function parseBatchRequest(array $payloads) : array
    {
        $results = [];

        foreach ($payloads as $payload) {
            $results[] = $this->parseRequest($payload);
        }

        return array_filter($results);
    }

    /**
     * @param array $payload
     *
     * @return mixed
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcParseException
     * @throws \Devim\Component\RpcServer\Exception\RpcServiceNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     * @throws \Devim\Component\RpcServer\Exception\RpcMethodNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidRequestException
     */
    private function parseRequest(array $payload)
    {
        list($serviceName, $methodName) = $this->extractServiceAndMethodNames($payload['method']);

        $service = $this->getService($serviceName);

        if (!method_exists($service, $methodName)) {
            throw new RpcMethodNotFoundException($methodName, $serviceName);
        }

        $params = $payload['params'] ?? [];
        $params = $this->extractParametersValue($service, $methodName, $params);

        foreach ($this->middleware as $middleware) {
            $middleware->execute($service, $methodName, $params);
        }

        $result = $this->invokeMethod($service, $methodName, $params);

        if (!$this->isNotification($payload)) {
            return ResponseBuilder::build($payload['id'], $result);
        }

        return '';
    }

    /**
     * @param string $method
     *
     * @return array
     */
    private function extractServiceAndMethodNames(string $method) : array
    {
        $methodInfo = explode('.', $method, 2);
        $serviceName = isset($methodInfo[1]) ? $methodInfo[0] : false;
        $methodName = $serviceName !== false ? $methodInfo[1] : $method;

        return [$serviceName, $methodName];
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws RpcServiceNotFoundException
     */
    private function getService(string $name)
    {
        if (!array_key_exists($name, $this->services)) {
            throw new RpcServiceNotFoundException($name);
        }

        list($className, $paramsClosure) = $this->services[$name];

        if (!array_key_exists($name, $this->servicesCache)) {
            $this->servicesCache[$name] = (new \ReflectionClass($className))->newInstanceArgs($paramsClosure());
        }

        return $this->servicesCache[$name];
    }

    /**
     * @param $service
     * @param string $method
     * @param array $params
     *
     * @return array
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     */
    private function extractParametersValue($service, string $method, array $params) : array
    {
        $results = [];

        $reflectionMethod = new \ReflectionMethod($service, $method);

        if (array_keys($params) === range(0, count($params) - 1)) {
            $results = $params;
        } else {
            foreach ($reflectionMethod->getParameters() as $parameter) {
                $paramName = $parameter->getName();

                if (array_key_exists($paramName, $params)) {
                    $results[] = $params[$paramName];
                } else {
                    if ($parameter->isDefaultValueAvailable()) {
                        $results[] = $parameter->getDefaultValue();
                    }
                }
            }
        }

        if (count($results) !== $reflectionMethod->getNumberOfParameters()) {
            throw new RpcInvalidParamsException('Invalid number of required parameters');
        }

        return $results;
    }

    /**
     * @param $service
     * @param string $method
     * @param array $params
     *
     * @return mixed
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     */
    private function invokeMethod($service, string $method, array $params)
    {
        $paramsValue = $this->extractParametersValue($service, $method, $params);

        return (new \ReflectionMethod($service, $method))->invokeArgs($service, $paramsValue);
    }

    private function isNotification(array $payload)
    {
        return !isset($payload['id']);
    }

    /**
     * @param int|null $id
     * @param \Throwable $exception
     *
     * @return array
     */
    private function handleExceptions($id, \Throwable $exception) : array
    {
        return ResponseBuilder::build($id, $exception);
    }
}
