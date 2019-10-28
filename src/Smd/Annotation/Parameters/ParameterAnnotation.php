<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Parameters;

use Devim\Component\RpcServer\Smd\Annotation\Service;
use Devim\Component\RpcServer\Smd\Annotation\SmdAnnotationInterface;
use Devim\Component\RpcServer\Smd\Exception;

/**
 * Parameter information class
 */
class ParameterAnnotation implements SmdAnnotationInterface{
    public $type;
    public $name = null;
    public $description;
    public $defaultValue = null;
    public $isOptional = false;
    
    public $usedDefinition = null;

    const STD_TYPE_ALIASES = [
        'int' => 'integer',
        'str' => 'string',
        'bool' => 'boolean',
        'float' => 'number'
    ];

    const STD_TYPES = ['boolean', 'integer', 'number', 'object', 'string', 'array'];

    /**
     * @var Service
     */
    public $service;

    /**
     * ParameterAnnotation constructor
     *
     * @param string $type
     * @param string $name
     * @param string $description
     * @param boolean $isOptional
     * @param Service $service
     */
    public function __construct(
        string $type, 
        string $name, 
        string $description, 
        bool $isOptional, 
        Service $service
    ) {   
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->isOptional = $isOptional;
        $this->service = $service;
    }
    
    /**
     * Проверяем тип на наличие ссылки
     *
     * @param array $smdInfo
     * @return array
     */
    public function checkType(array $smdInfo) : array
    {
        if (!in_array($smdInfo['type'], static::STD_TYPES)) {
            if(!isset(static::STD_TYPE_ALIASES[$smdInfo['type']])){
                $typeDefinitions = $this->service->definitionsManager->resolveSmdDefinitionsByName($smdInfo['type']);
                $smdInfo['$ref'] = '#/definitions/' . $smdInfo['type'];
                $smdInfo['type'] = 'object';
                $smdInfo['definitions'] = $typeDefinitions;
            } else {
                $smdInfo['type'] = static::STD_TYPE_ALIASES[$smdInfo['type']];
            }
        }
        
        return $smdInfo;        
    }

    /**
     * Проверяем тип элементов массива на наличие ссылки
     *
     * @param array $smdInfo
     * @return array
     */
    public function setItemType(array $smdInfo) : array
    {
        $smdInfo['items'] = [];

        if (!in_array($this->itemType, static::STD_TYPES)) {
            if(!isset(static::STD_TYPE_ALIASES[$this->itemType])){
                $usedDefinitions = $this->service->definitionsManager->resolveSmdDefinitionsByName($this->itemType);
                $smdInfo['definitions'] = $usedDefinitions;
                $smdInfo['items']['$ref'] = '#/definitions/' . $this->itemType;
            } else {
                $smdInfo['items']['type'] = static::STD_TYPE_ALIASES[$this->itemType];
            }
        } else {
            $smdInfo['items']['type'] = $this->itemType;
        }

       return $smdInfo;
    }

    /**
     * Проверяем объект на необходимость копирования полей по ссылке
     *
     * @param array $smdInfo
     * @return array
     */
    public function setProperties(array $smdInfo) : array
    {
        if(!isset($smdInfo['properties']) && !empty($this->objectRef)){
            $usedDefinitions = $this->service->definitionsManager->resolveSmdDefinitionsByName($this->objectRef);
            $smdInfo['properties'] = $usedDefinitions[$this->objectRef]['properties'];
            unset($usedDefinitions[$this->objectRef]);
            if(!empty($usedDefinitions)){
                $smdInfo['definitions'] = $usedDefinitions;
            }
        }

       return $smdInfo;
    }

    /**
     * Parameter information in SMD format
     * @return array
     */
    public function getSmdInfo(): array
    {
        $smdInfo = [
            'type' => $this->type,
            'name' => $this->name
        ];
        if(!is_null($this->description)){
            $smdInfo['description'] = $this->description;
        }
        if(!is_null($this->defaultValue)){
            $smdInfo['default'] = $this->defaultValue;
        }

        if($this->isOptional){
            $smdInfo['optional'] = true;
        }

        return $this->checkType($smdInfo);
    }
}