<?php

namespace Devimteam\Component\RpcServer\Exception;

/**
 * Class RpcParseException
 */
class RpcParseException extends RpcException
{
    /**
     * RpcParseException constructor
     */
    public function __construct()
    {
        parent::__construct('Parse error', self::PARSE_ERROR);
    }
}
