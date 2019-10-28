<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Returns;

use Devim\Component\RpcServer\Smd\Annotation\Service;

class ObjectReturnAnnotation extends ReturnAnnotation{
    
    public $objectRef;
    
    public function __construct(string $objectRef, string $name, string $description, bool $isOptional, Service $service)
    {   
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