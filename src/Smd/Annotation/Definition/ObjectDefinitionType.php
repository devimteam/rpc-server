<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ObjectDefinitionType extends AbstractDefinitionType
{
    
    /**
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $properties;
    
    /**
     *
     * @var string
     */
    public $ref;
    
    public function getSmdInfo() {
        $info = parent::getSmdInfo();
        
        $hasRef = !empty($this->ref);
        $hasProperties = !empty($this->properties);
        
        if ($hasRef && $hasProperties) {
            throw new \Exception('oops 1');
        }
        
        if ($hasRef) {
            $info['$ref'] = '#/definitions/' . $this->ref;
        } else if ($hasProperties) {
            $info['properties'] = $this->getSmdProperties();
        } else {
            throw new \Exception('oops 2');
        }
        
        return $info;
    }

    private function getSmdProperties()
    {
        $result = [];
        foreach ($this->properties as $item) {
            $result[$item->name] = $item->getSmdInfo();
        }
        return $result;
    }
    
}
