<?php

namespace Devim\Component\RpcServer\Smd;

/**
 * Interface SmdGeneratorInterface
 */
interface SmdGeneratorInterface {
    /**
     * @param \Generator $serviceAnnotationGenerator
     * 
     * @return array
     */
    public function run(\Generator $serviceAnnotationGenerator): array;
}