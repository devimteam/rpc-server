<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameter;

use Devim\Component\RpcServer\Smd\Annotation\Definition\HasDefinitions;
use Devim\Component\RpcServer\Smd\Exception;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ArrayParameterType extends AbstractParameterType
{
    use HasDefinitions;
    
    const STD_TYPES = ['boolean', 'integer', 'number', 'object', 'string'];
    
    /**
     * @Required
     * 
     * @var string
     */
    public $type;
    
    /**
     * @return array
     * @throws Exception\SmdInvalidDefinitionRef
     */
    public function getSmdInfo(): array
    {
        $info = parent::getSmdInfo();
        
        if (in_array($this->type, static::STD_TYPES)) {
            $info['items'] = [
                'type' => $this->type,
            ];
        } else if (!empty($this->definitions)) {
            $info['definitions'] = $this->getSmdDefinitions();
            
            if (!isset($info['definitions'][$this->type])) {
                throw new Exception\SmdInvalidDefinitionRef($this->type);
            }
            
            $info['items'] = [
                '$ref' => '#/definitions/' . $this->type,
            ];
        }
        
        return $info;
    }
}
