<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Definitions
{
    /**
     * @Required
     *
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $properties;
}
