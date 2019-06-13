<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Parameter;

use Devim\Component\RpcServer\Smd\Annotation\AbstractType;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class AbstractParameterType extends AbstractType
{

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

   /**
     * 
     * @return string
     */
    public function getTypeName() {
        return parent::getTypeName('ParameterType');
    }
}
