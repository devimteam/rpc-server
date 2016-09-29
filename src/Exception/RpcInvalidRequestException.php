<?php

namespace Devimteam\Component\RpcServer\Exception;

/**
 * Class RpcInvalidRequestException.
 */
class RpcInvalidRequestException extends RpcException
{
    /**
     * RpcInvalidRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('Invalid Request', self::INVALID_REQUEST);
    }
}
