<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Returns;

use Devim\Component\RpcServer\Smd\Annotation\Parameters\ParameterAnnotation;
use Devim\Component\RpcServer\Smd\Annotation\Service;

class ReturnAnnotation extends ParameterAnnotation
{
    public $type;
    public $description;
    public $defaultValue = null;
    public $isOptional = false;

    public function __construct(
        string $type, 
        string $name, 
        string $description, 
        bool $isOptional, 
        Service $service
    ) {   
        $this->type = $type;
        $this->description = $description;
        $this->isOptional = $isOptional;
        $this->service = $service;

        $this->name = $name;
    }

    /**
     * Parameter information in SMD format
     * @return array
     */
    public function getSmdInfo(): array
    {
        $smdInfo = parent::getSmdInfo();
        $smdInfo['type'] = $this->type;
        
        unset($smdInfo['name']);

        return $smdInfo;
    }
}