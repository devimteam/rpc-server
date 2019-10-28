<?php
namespace Devim\Component\RpcServer\Smd\Annotation;

interface SmdAnnotationInterface
{
    /**
     * Annotation information in SMD format
     * @return array
     */
    public function getSmdInfo(): array;
}