<?php

namespace Devimteam\Component\RpcServer\Exception;

/**
 * Class RpcInvalidParamsException.
 */
class RpcInvalidParamsException extends RpcException
{
    /**
     * RpcControllerNotFoundException constructor.
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        parent::__construct('Invalid parameters', self::INVALID_PARAMS, null, $data);
    }
}
