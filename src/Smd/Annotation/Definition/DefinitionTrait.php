<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

trait DefinitionTrait {
    /**
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $definitions = [];
    
    private function getSmdDefinitions() {
        $result = [];
        foreach ($this->definitions as $item) {
            $result[$item->name] = $item->getSmdInfo();
        }
        return $result;
    }
    
}
