<?php

namespace Devim\Component\RpcServer\Exception;

/**
 * Class RpcInternalErrorException.
 */
class RpcInternalErrorException extends RpcException
{
    /**
     * RpcInternalErrorException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(sprintf('Internal error - "%s"', $message), self::INTERNAL_ERROR);
    }
}
