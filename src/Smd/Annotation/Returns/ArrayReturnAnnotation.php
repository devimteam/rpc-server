<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Returns;

use Devim\Component\RpcServer\Smd\Annotation\Service;

class ArrayReturnAnnotation extends ReturnAnnotation{
    public $itemType;
    
    
    public function __construct(string $itemType, string $name, string $description, bool $isOptional, Service $service)
    {   
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