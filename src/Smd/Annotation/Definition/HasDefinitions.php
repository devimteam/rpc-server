<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

/**
 * Trait HasDefinitions
 */
trait HasDefinitions {
    /**
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $definitions = [];
    
    /**
     * @param string $definitionName
     * @return Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType|null
     */
    public function findDefinitionByName(string $definitionName)
    {
        foreach ($this->definitions as $item) {
            if($item->name == $definitionName){
                return $item;
            }
        }

        return null;
    }

    
    /**
     * @return array
     */
    public function getSmdDefinitions(): array 
    {
        $result = [];
        foreach ($this->definitions as $item) {
            $result[$item->name] = $item->getSmdInfo($this);
        }
        
        return $result;
    }
    
}
