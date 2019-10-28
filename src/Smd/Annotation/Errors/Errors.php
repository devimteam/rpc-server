<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Errors;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;

class Errors extends AbstractSmdAnnotationsArray
{
    /**
     * @var array<ErrorAnnotation>
     */
    public $items;

    /**
     * @return array
     */
    public function getSmdInfo(): array
    {
        $info = [];
        foreach ($this->items as $item) {
            if(!$item->notRpc && !empty($item->code)){
                $info[$item->code] = $item->description;
            }
        }
        return $info;
    }
}
