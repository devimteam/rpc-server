<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Parameters
{
    /**
     * @Required
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Parameter\AbstractParameterType>
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
