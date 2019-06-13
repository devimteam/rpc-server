<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameter;

use Devim\Component\RpcServer\Smd\Annotation\Definition\DefinitionTrait;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ObjectParameterType extends AbstractParameterType
{
    use DefinitionTrait;
    
    /**
     *
     * @var string
     */
    public $ref;
    
    public function getSmdInfo() {
        $info = parent::getSmdInfo();
        
        if (!empty($this->definitions)) {
            $info['definitions'] = $this->getSmdDefinitions();
        }
        
        if (!empty($this->ref)) {
            $info['$ref'] = '#/definitions/' . $this->ref;
        }
        
        return $info;
    }
    
}
