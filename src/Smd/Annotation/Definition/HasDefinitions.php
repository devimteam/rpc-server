<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

/**
 * Trait HasDefinitions
 */
trait HasDefinitions {
    /**
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $definitions = [];
    
    /**
     * @return array
     */
    private function getSmdDefinitions(): array 
    {
        $result = [];
        foreach ($this->definitions as $item) {
            $result[$item->name] = $item->getSmdInfo();
        }
        return $result;
    }
    
}
