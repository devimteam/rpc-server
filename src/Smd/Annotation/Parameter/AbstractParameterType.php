<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameter;

use Devim\Component\RpcServer\Smd\Annotation\AbstractType;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class AbstractParameterType extends AbstractType
{

    const CLASS_NAME_SUFFIX = 'ParameterType';
    
    /**
     *
     * @var bool
     */
    public $optional = false;

    /**
     *
     * @var mixed
     */
    public $default = null;
}
