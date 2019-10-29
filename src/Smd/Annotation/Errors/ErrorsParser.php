<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Errors;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;
use Devim\Component\RpcServer\Smd\Annotation\AnnotationParser;
use Devim\Component\RpcServer\Smd\Annotation\SmdAnnotationInterface;

class ErrorsParser extends AnnotationParser
{

    protected $regularPattern = '/^(?<type>[\w\|]+)[ ]+(?<description>.*)$/';
    protected $annotationName = "throws";


    public function createSmdObjectContainer() : AbstractSmdAnnotationsArray
    {
       return new Errors(); 
    }

    public function newSmdObjectFromParams(array $params) : SmdAnnotationInterface
    {
        $parsedParams = $params['params'];

        $paramCode = $parsedParams['code'] ?? "";

        $paramDescription = $parsedParams['description'] ?? "";
        
        $notRpc = $parsedParams['notRpc'] ?? false;

        return new ErrorAnnotation($paramCode,  $paramDescription, $notRpc);
    }
}