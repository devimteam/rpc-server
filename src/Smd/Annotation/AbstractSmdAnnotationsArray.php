<?php
namespace Devim\Component\RpcServer\Smd\Annotation;

abstract class AbstractSmdAnnotationsArray implements SmdAnnotationInterface
{
    /**
     * @var array<SmdAnnotationInterface>
     */
    public $items;

    /**
     * @return array
     */
    public function getSmdInfo(): array
    {
        $info = [];
        foreach ($this->items as $item) {
            $info[] = $item->getSmdInfo();
        }
        return $info;
    }
}