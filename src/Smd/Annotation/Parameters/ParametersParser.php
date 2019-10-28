<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Parameters;

use Devim\Component\RpcServer\Smd\Annotation\AbstractSmdAnnotationsArray;
use Devim\Component\RpcServer\Smd\Annotation\AnnotationParser;
use Devim\Component\RpcServer\Smd\Annotation\Parameters\Parameters;
use Devim\Component\RpcServer\Smd\Annotation\SmdAnnotationInterface;

class ParametersParser extends AnnotationParser
{

    protected $regularPattern = '/^(?<type>[\w\|]+)[ ]+\$(?<name>[\w]+)(?<description>.*)$/';
    protected $annotationName = "param";


    public function createSmdObjectContainer() : AbstractSmdAnnotationsArray
    {
        return new Parameters(); 
    }

    public function newSmdObjectFromParams(array $params) : SmdAnnotationInterface
    {
        $parsedParams = $params['params'];

        $paramType = $parsedParams['type'];
        $paramName = $parsedParams['name'];

        $paramDescription = $parsedParams['description'];

        $paramDescription = trim(str_replace($params['brackets'], "", $paramDescription));

        $paramIsOptional = strpos($paramType,'|null') !== false || strpos($paramType,'null|') !== false;
        $paramType = $parsedParams['ref'] ?? $paramType;
        $paramType = str_replace(['|null', 'null|'], "", $paramType);
        if(isset($parsedParams['objectRef'])){
            return new ObjectParameterAnnotation($parsedParams['objectRef'], $paramName, $paramDescription, $paramIsOptional, $this->service);
        }
        if($paramType == 'array'){
            return new ArrayParameterAnnotation($parsedParams['itemType'] ?? "", $paramName, $paramDescription, $paramIsOptional, $this->service);
        }
        return new ParameterAnnotation($paramType, $paramName, $paramDescription, $paramIsOptional, $this->service);
    }
}