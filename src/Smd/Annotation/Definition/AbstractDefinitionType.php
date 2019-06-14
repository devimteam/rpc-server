<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

use Doctrine\Common\Annotations\Annotation\Required;

use Devim\Component\RpcServer\Smd\Annotation\AbstractType;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class AbstractDefinitionType extends AbstractType
{
    const CLASS_NAME_SUFFIX = 'DefinitionType';   
}
