<?php

namespace Devim\Component\RpcServer\Exception;

/**
 * Class RpcControllerNotFoundException.
 */
class RpcServiceNotFoundException extends RpcException
{
    /**
     * RpcControllerNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Service "%s" not found', $name), self::NOT_FOUND);
    }
}
