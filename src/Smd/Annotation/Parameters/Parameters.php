<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameters;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;


class Parameters extends AbstractSmdAnnotationsArray
{
    /**
     * @var array<ParameterAnnotation>
     */
    public $items;
}
