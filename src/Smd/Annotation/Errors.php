<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
final class Errors
{
    /**
     * @Required
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Error>
     */
    public $items;
    
    public function getSmdInfo()
    {
        $result = [];
        foreach ($this->items as $error) {
            $result[$error->code] = $error->description;
        }
        return $result;
    }
}
