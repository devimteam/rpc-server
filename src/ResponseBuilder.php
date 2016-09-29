<?php

namespace Devimteam\Component\RpcServer;

use Devimteam\Component\RpcServer\Exception\RpcException;

/**
 * Class ResponseBuilder
 */
class ResponseBuilder
{
    /**
     * @param int|null $id
     * @param mixed $data
     *
     * @return array
     */
    public static function build($id, $data) : array
    {
        $response = ['jsonrpc' => RpcServer::JSON_RPC_VERSION];

        if ($data instanceof \Throwable) {
            $response['error'] = self::buildError($data);
        } else {
            $response['result'] = $data;
        }

        $response['id'] = $id;

        return $response;
    }

    /**
     * @param \Throwable $exception
     *
     * @return array
     */
    private static function buildError(\Throwable $exception) : array
    {
        $response = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof RpcException && null !== $exception->getData()) {
            $response['data'] = $exception->getData();
        }

        return $response;
    }
}