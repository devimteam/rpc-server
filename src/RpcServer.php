<?php

namespace Devim\Component\RpcServer;

use Devim\Component\RpcServer\Exception\RpcInvalidParamsException;
use Devim\Component\RpcServer\Exception\RpcInvalidRequestException;
use Devim\Component\RpcServer\Exception\RpcMethodNotFoundException;
use Devim\Component\RpcServer\Exception\RpcParseException;
use Devim\Component\RpcServer\Exception\RpcServiceExistsException;
use Devim\Component\RpcServer\Exception\RpcServiceNotFoundException;
use Devim\Component\RpcServer\Smd\SmdGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\Reader as AnnotationReaderInterface;

/**
 * Class RpcServer
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
     * @var AnnotationReaderInterface
     */
    private $annotationReader;
    
    /**
     * @var SmdGenerator 
     */
    private $smdGenerator;
    
    /**
     * 
     * @param AnnotationReaderInterface $annotationReader
     * @param SmdGeneratorInterface $smdGenerator
     */
    public function __construct(AnnotationReaderInterface $annotationReader, SmdGeneratorInterface $smdGenerator)
    {
        $this->annotationReader = $annotationReader;
        $this->smdGenerator = $smdGenerator;
    }
    
    /**
     * @param string $className
     * @param \Closure $parametersCallback
     *
     * @return $this
     * @throws \Devim\Component\RpcServer\Exception\RpcServiceExistsException
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
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcServiceNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcParseException
     * @throws \Devim\Component\RpcServer\Exception\RpcMethodNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidRequestException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     * @throws \LogicException
     */
    public function run(Request $request) : JsonResponse
    {
        $response = [];

        if ($this->isSmdRequest($request)) {
            $response = $this->smdGenerator->run($this->serviceAnnotationGenerator());
        } else {
            $payload = json_decode($request->getContent(), true);

            if ($this->isBatchRequest($payload)) {
                foreach ($payload as $item) {
                    $response[] = $this->doRun($item);
                }
                if (count($response) === 1) {
                    $response = reset($response);
                }
            } else {
                $response = $this->doRun($payload);
            }
        }

        return JsonResponse::create($response);
    }

    /**
     * @param mixed $payload
     *
     * @return mixed
     *
     * @throws \Devim\Component\RpcServer\Exception\RpcServiceNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcParseException
     * @throws \Devim\Component\RpcServer\Exception\RpcMethodNotFoundException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidParamsException
     * @throws \Devim\Component\RpcServer\Exception\RpcInvalidRequestException
     */
    private function doRun($payload)
    {
        try {
            $this->validatePayload($payload);
        } catch (\Throwable $e) {
            return $this->handleExceptions($payload, $e);
        }

        try {
            return $this->parseRequest($payload);
        } catch (\Throwable $e) {
            return $this->handleExceptions($payload, $e);
        }
    }

    /**
     * @param mixed $payload
     *
     * @throws RpcInvalidRequestException
     * @throws RpcParseException
     */
    private function validatePayload(&$payload)
    {
        if (null === $payload) {
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
     * 
     * @return bool
     */
    private function isSmdRequest(Request $request)
    {
        return $request->query->has('smd');
    }
    
    /**
     * @param mixed $payload
     *
     * @return bool
     */
    private function isBatchRequest($payload) : bool
    {
        return is_array($payload) && array_keys($payload) === range(0, count($payload) - 1);
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

        return ResponseBuilder::build($this->extractRequestId($payload), $result);
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

    /**
     * @param $payload
     * @param \Throwable $exception
     *
     * @return array
     */
    private function handleExceptions($payload, \Throwable $exception) : array
    {
        return ResponseBuilder::build($this->extractRequestId($payload), $exception);
    }

    /**
     * @param $payload
     *
     * @return null|int
     */
    private function extractRequestId($payload)
    {
        return $payload['id'] ?? null;
    }
    
    private function serviceAnnotationGenerator() {
        foreach ($this->services as $name => [$className, $parametersCallback]) {
            $class = new \ReflectionClass($className);
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $annotation = $this->annotationReader->getMethodAnnotation($method, \Devim\Component\RpcServer\Smd\Annotation\Service::class);

                if (!empty($annotation)) {
                    $smdName = $name . '.' . $method->getName();
                    yield $smdName => $annotation;
                }
            }
        }
    }
}
