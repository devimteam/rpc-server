<?php

namespace Devim\Component\RpcServer\Smd;

/**
 * Description of SmdGenerator
 *
 * @author eugene
 */
class SmdGenerator implements SmdGeneratorInterface
{
    public function run(\Generator $serviceAnnotationGenerator) {
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
