<?php

namespace Devim\Component\RpcServer\Smd\Exception;

/**
 * Class SmdInvalidDefinition
 */
class SmdInvalidDefinitionRef extends SmdException
{
    /**
     * @param string $ref
     */
    public function __construct(string $ref)
    {
        parent::__construct(sprintf('Definition not found: "%s"', $ref), self::INVALID_DEFINITION_REF);
    }    
}
