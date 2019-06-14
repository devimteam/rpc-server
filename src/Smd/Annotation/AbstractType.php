<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

use Devim\Component\RpcServer\Smd\Exception;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
abstract class AbstractType
{

    const CLASS_NAME_SUFFIX = '';
    
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @Required
     *
     * @var string
     */
    public $description;

    /**
     * @return string
     * @throws SmdInvalidClassNameException
     */
    public function getTypeName(): string
    {
        $rx = '/^(.+)'.static::CLASS_NAME_SUFFIX.'$/';
        $className = (new \ReflectionClass($this))->getShortName();
        
        if (!preg_match($rx, $className, $matches)) {
            throw new Exception\SmdInvalidClassNameException($className, static::CLASS_NAME_SUFFIX);
        }
        
        return lcfirst($matches[1]);
    }
    
    /**
     * @return array
     */
    public function getSmdInfo(): array
    {
        return [
            'type' => $this->getTypeName(),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
