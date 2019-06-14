<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Error
{
    /**
     * @Required
     *
     * @var string
     */
    public $code;

    /**
     * @Required
     *
     * @var string
     */
    public $description;
}
