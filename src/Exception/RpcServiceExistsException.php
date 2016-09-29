<?php

namespace Devimteam\Component\RpcServer\Exception;

/**
 * Class RpcControllerExistsException.
 */
class RpcServiceExistsException extends \RuntimeException
{
    /**
     * RpcControllerExistsException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf('RPC controller "%s" exists', $name));
    }
}
