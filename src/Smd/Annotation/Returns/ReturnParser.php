<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Returns;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;
use Devim\Component\RpcServer\Smd\Annotation\AnnotationParser;
use Devim\Component\RpcServer\Smd\Annotation\SmdAnnotationInterface;

class ReturnParser extends AnnotationParser
{

    protected $regularPattern = '/^(?<type>[\w\|]+)[ ]+(?<description>.*)$/';
    protected $annotationName = "return";


    public function createSmdObjectContainer() : AbstractSmdAnnotationsArray
    {
        return new Returns(); 
    }

    public function newSmdObjectFromParams(array $params) : SmdAnnotationInterface
    {
        $parsedParams = $params['params'];

        $paramType = $parsedParams['type'];
        $paramName = $parsedParams['name'] ?? "";

        $paramDescription = $parsedParams['description'];

        $paramIsOptional = strpos($paramType,'|null') !== false;
        $paramType = $parsedParams['ref'] ?? $paramType;
        $paramType = str_replace('|null', "", $paramType);

        if(isset($parsedParams['objectRef'])){
            return new ObjectReturnAnnotation($parsedParams['objectRef'], $paramName, $paramDescription, $paramIsOptional, $this->service);
        }
        if($paramType == 'array'){
            return new ArrayReturnAnnotation($parsedParams['itemType'] ?? "", $paramName, $paramDescription, $paramIsOptional, $this->service);
        }
        return new ReturnAnnotation($paramType,  $paramName,  $paramDescription, $paramIsOptional, $this->service);
    }
}