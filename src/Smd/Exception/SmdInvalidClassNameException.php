<?php

namespace Devim\Component\RpcServer\Smd\Exception;

/**
 * Class SmdInvalidClassNameException
 */
class SmdInvalidClassNameException extends SmdException
{

    /**
     * @param string $className
     * @param string $classNameSuffix
     */
    public function __construct(string $className, string $classNameSuffix)
    {
        parent::__construct(sprintf('Invalid class name "%s". Class name must end with "%s"', $className, $classNameSuffix), self::INVALID_CLASS_NAME);
    }
}
