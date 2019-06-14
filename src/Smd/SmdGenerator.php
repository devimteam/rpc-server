<?php

namespace Devim\Component\RpcServer\Smd;

/**
 * Class SmdGenerator
 */
class SmdGenerator implements SmdGeneratorInterface
{
    /**
     * @var string 
     */
    private $transport;
    
    /**
     * @var string 
     */
    private $envelope;

    /**
     * @var string 
     */
    private $contentType;

    /**
     * @var string 
     */
    private $SMDVersion;

    /**
     * @var string 
     */
    private $target;
    
    /**
     * @param string $target
     * @param string $transport
     * @param string $envelope
     * @param string $contentType
     * @param string $SMDVersion
     */
    public function __construct($target, $transport = 'POST', $envelope = 'JSON-RPC-2.0', $contentType = 'application/json', $SMDVersion = '2.0')
    {
        $this->target = $target;
        $this->transport = $transport;
        $this->envelope = $envelope;
        $this->contentType = $contentType;
        $this->SMDVersion = $SMDVersion;
    }
    
    /**
     * @param \Generator $serviceAnnotationGenerator
     * 
     * @return array
     */
    public function run(\Generator $serviceAnnotationGenerator): array
    {
        $services = [];
        foreach ($serviceAnnotationGenerator as $name => $annotation) {
            $services[$name] = $annotation->getSmdInfo();
        }
        
        return [
            'transport' => $this->transport,
            'envelope' => $this->envelope,
            'contentType' => $this->contentType,
            'SMDVersion' => $this->SMDVersion,
            'target' => $this->target,
            'services' => $services,
        ];

    }
}
