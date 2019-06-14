<?php

namespace Devim\Component\RpcServer\Smd\Exception;

/**
 * Class SmdInvalidDefinition
 */
class SmdInvalidDefinition extends SmdException
{
    /**
     * @param string $className
     */
    public function __construct(string $message)
    {
        parent::__construct($message, self::INVALID_DEFINITION);
    }    
}
