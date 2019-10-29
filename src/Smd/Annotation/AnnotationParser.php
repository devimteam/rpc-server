<?php
namespace Devim\Component\RpcServer\Smd\Annotation;

use Devim\Component\RpcServer\Smd\BracketsParser\BracketsParser;

abstract class AnnotationParser
{
    public $docBlock;
    
    /**
     * Регулярное выражение для разбора строки аннотации
     *
     * @var string
     */
    protected $regularPattern = "";

    protected $annotationName = "";
    
    /**
     * Undocumented variable
     *
     * @var AbstractSmdAnnotationsArray
     */
    protected $smdObjectContainer;
    /**
     * Объект аннотации сервиса
     *
     * @var Service
     */
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->smdObjectContainer = $this->createSmdObjectContainer();   
    }

    /**
     * Создаём объект контейнера для SMD объектов в $this->smdObjectContainer
     *
     * @return AbstractSmdAnnotationsArray
     */
    protected abstract function createSmdObjectContainer() : AbstractSmdAnnotationsArray;
    
    /**
     * Создаём новыё SMD объект для контейнера объектов
     * 
     * @param array $params разобранные из аннотации параметры в виде массива ['params' => параметры, 'brackets' => строки_скобок]
     * @return SmdAnnotationInterface
     */
    public abstract function newSmdObjectFromParams(array $params) : SmdAnnotationInterface;
    /**
     * Вытаскивает все строки по имени аннотации
     *
     * @return array
     */
    public function getAnnotationStrings() : array
    {
        $docLines = explode("\n", $this->docBlock);
        $annotationName = $this->annotationName;
        $paramLines = array_filter($docLines, function($var) use ($annotationName){
            return strpos($var, '@'.$annotationName." " ) !== false;
        });

        $paramLines = array_map(function($var) use($annotationName){
            $var = substr($var,strpos($var, '@'.$annotationName ) + strlen('@'.$annotationName." "));
            return  preg_replace('|\s+|', ' ', trim($var));
        }, $paramLines);
        
        return $paramLines;
    }

    /**
     * Разбирает строку аннотации на параметры
     * Параметры в скобках замещают параметры из регулярного выражения
     *
     * @param string $annotationString
     * @return array Разобранные параметры и массив строчек скобок
     */
    public function parseStringByPattern(string $annotationString) : array
    {
        $regularFoundParams = [];
        $bracketsFoundArray = []; 

        preg_match_all($this->regularPattern, $annotationString, $regularFoundParams);
        preg_match_all('/(?<brackets>\[[^\[\]]+\])/', $annotationString, $bracketsFoundArray);

        $bracketsFoundArray = array_filter($bracketsFoundArray , 'is_string', ARRAY_FILTER_USE_KEY);
        $regularFoundParams = array_filter($regularFoundParams , 'is_string', ARRAY_FILTER_USE_KEY);
        
        $regularFoundParams = array_map(function($var) {
            return $var[0] ?? "";
        }, $regularFoundParams);

        $bracketsParser = new BracketsParser($annotationString);
        $paramsInBrackets = $bracketsParser->getFirst();
        $allParams = array_merge($regularFoundParams, $paramsInBrackets);

        return [
            'params' =>  $allParams,
            'brackets' => $bracketsFoundArray['brackets']
        ];
    }

    

    public function parseDocBlock(string $docBlock) : AbstractSmdAnnotationsArray
    {
        $this->docBlock = $docBlock;
        $allAnnotationStrings = $this->getAnnotationStrings();
        $annotationParamsArray = array_map(function($value){return self::parseStringByPattern($value);}, $allAnnotationStrings);

        foreach($annotationParamsArray as $annotationParams){
            $this->smdObjectContainer->items[] = $this->newSmdObjectFromParams($annotationParams);
        }
        
        return $this->smdObjectContainer;
    }
}
