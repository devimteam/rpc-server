<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
abstract class AbstractType
{

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
     * 
     * @return string
     */
    public function getTypeName($prefix='') {
        $className = (new \ReflectionClass($this))->getShortName();
        if (!preg_match('/^(.+)'.$prefix.'$/', $className, $matches)) {
            throw new \Exception($className);
        }
        
        return lcfirst($matches[1]);
    }
    
    public function getSmdInfo() {
        return [
            'type' => $this->getTypeName(),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
