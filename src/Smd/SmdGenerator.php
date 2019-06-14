<?php

namespace Devim\Component\RpcServer\Smd;

/**
 * Class SmdGenerator
 */
class SmdGenerator implements SmdGeneratorInterface
{
    /**
     * @param \Generator $serviceAnnotationGenerator
     * 
     * @return array
     */
    public function run(\Generator $serviceAnnotationGenerator): array {
        $services = [];
        
        foreach ($serviceAnnotationGenerator as $name => $annotation) {
            $services[$name] = $annotation->getSmdInfo();
        }
        
        return [
            'transport' => 'POST',
            'envelope' => 'JSON-RPC-2.0',
            'contentType' => '',
            'SMDVersion' => '',
            'target' => '',
            'services' => $services,
        ];

    }
}
