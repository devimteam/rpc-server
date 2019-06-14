<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ArrayDefinitionType extends AbstractDefinitionType
{

    const STD_TYPES = ['boolean', 'integer', 'number', 'object', 'string'];
    
    /**
     * @Required
     * 
     * @var string
     */
    public $type;
    
    public function getSmdInfo(): array {
        $info = parent::getSmdInfo();
        
        if (in_array($this->type, static::STD_TYPES)) {
            $info['items'] = [
                'type' => $this->type,
            ];
        } else {
            $info['items'] = [
                '$ref' => '#/definitions/' . $this->type,
            ];
        }
        
        return $info;
    }
}
