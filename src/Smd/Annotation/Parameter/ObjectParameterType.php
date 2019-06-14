<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameter;

use Devim\Component\RpcServer\Smd\Annotation\Definition\HasDefinitions;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ObjectParameterType extends AbstractParameterType
{
    use HasDefinitions;
    
    /**
     * @var string
     */
    public $ref;
    
    /**
     * @return array
     */
    public function getSmdInfo(): array {
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
