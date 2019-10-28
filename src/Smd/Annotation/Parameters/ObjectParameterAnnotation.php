<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Parameters;

use Devim\Component\RpcServer\Smd\Annotation\Service;

class ObjectParameterAnnotation extends ParameterAnnotation{
    
    public $objectRef;
    
    /**
     * ObjectParameterAnnotation constructor
     *
     * @param string $objectRef
     * @param string $name
     * @param string $description
     * @param boolean $isOptional
     * @param Service $service
     */
    public function __construct(
        string $objectRef, 
        string $name, 
        string $description, 
        bool $isOptional, 
        Service $service
    ) {   
        parent::__construct('object', $name, $description,$isOptional, $service);
        $this->objectRef = $objectRef;
    }

    /**
     * Parameter information in SMD format
     * @return array
     */
    public function getSmdInfo(): array
    {
        $smdInfo = parent::getSmdInfo();
        $smdInfo = $this->setProperties($smdInfo);

        return $smdInfo;
    }
}