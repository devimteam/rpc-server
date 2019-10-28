<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Returns;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;


class Returns extends AbstractSmdAnnotationsArray
{
    /**
     * @var array<ReturnAnnotation>
     */
    public $items;

    /**
     * @return array
     */
    public function getSmdInfo(): array
    {
        return $this->items[0]->getSmdInfo();
    }
}
