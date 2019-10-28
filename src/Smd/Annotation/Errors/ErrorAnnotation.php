<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Errors;

use Devim\Component\RpcServer\Smd\Annotation\Service;
use Devim\Component\RpcServer\Smd\Annotation\SmdAnnotationInterface;
use Devim\Component\RpcServer\Smd\Exception;

/**
 * Error information class
 */
class ErrorAnnotation implements SmdAnnotationInterface{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $notRpc = false;

    public function __construct(string $code,string $description,bool $notRpc)
    {   
        $this->code = $code;
        $this->description = $description;
        $this->notRpc = $notRpc; 
    }

    /**
     * Error information in SMD format
     * @return array
     */
    public function getSmdInfo(): array
    {
        $smdInfo = [
            'code' => $this->code,
            'description' => $this->description
        ];

        return $smdInfo;
    }
}