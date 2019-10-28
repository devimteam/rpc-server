<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Parameters;

use Devim\Component\RpcServer\Smd\Annotation\Service;

class ArrayParameterAnnotation extends ParameterAnnotation{
    public $itemType;
    
    /**
     * ArrayParameterAnnotation constructor
     *
     * @param string $itemType
     * @param string $name
     * @param string $description
     * @param boolean $isOptional
     * @param Service $service
     */
    public function __construct(
        string $itemType, 
        string $name, 
        string $description, 
        bool $isOptional, 
        Service $service
    ) {   
        parent::__construct('array', $name, $description,$isOptional, $service);
        $this->itemType = $itemType;
    }

    

    /**
     * Parameter information in SMD format
     * @return array
     */
    public function getSmdInfo(): array
    {
        $smdInfo = parent::getSmdInfo();

        $smdInfo = $this->setItemType($smdInfo);

        return $smdInfo;
    }
}