<?php

namespace Devimteam\Component\RpcServer;

/**
 * Interface MiddlewareInterface
 */
interface MiddlewareInterface
{
    /**
     * @param mixed $service
     * @param string $methodName
     * @param array $params
     */
    public function execute($service, string $methodName, array $params);
}
