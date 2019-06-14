<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Service
{
    /**
     * @Required
     *
     * @var string
     */
    public $description;

    /**
     * @var \Devim\Component\RpcServer\Smd\Annotation\Parameters
     */
    public $parameters;

    /**
     * @Required
     *
     * @var \Devim\Component\RpcServer\Smd\Annotation\Parameter\AbstractParameterType
     */
    public $returns;

    /**
     * @Required
     *
     * @var \Devim\Component\RpcServer\Smd\Annotation\Errors
     */
    public $errors;

    /**
     * @return array
     */
    public function getSmdInfo(): array
    {
        return [
            'description' => $this->description,
            'parameters' => $this->parameters->getSmdInfo(),
            'returns' => $this->returns->getSmdInfo(),
            'errors' => $this->errors->getSmdInfo(),
        ];
    }
}
