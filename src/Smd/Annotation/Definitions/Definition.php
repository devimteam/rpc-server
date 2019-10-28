<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Definitions;

class Definition
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $usedRefs = [];

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var array
     */
    public $oneOf = [];
    
    /**
     * @var array
     */
    public $items = [];

    /**
     * @var DefinitionsManager
     */
    public $definitionsManager;

    public function __construct(DefinitionsManager $definitionsManager)
    {
        $this->definitionsManager = $definitionsManager;
    }

    /**
     * Разбираем массив на предмет знакомых полей
     *
     * @param array $jsonArray
     * @return void
     */
    public function parseFromJsonArray(array $jsonArray)
    {
        $this->type = $jsonArray['type'];
        $this->description = $jsonArray['description'] ?? "";
        $this->name = $jsonArray['name'];
        $this->properties = $jsonArray['properties'] ?? [];
        $this->oneOf = $jsonArray['oneOf'] ?? [];
        $this->items = $jsonArray['items'] ?? [];
        
        $this->checkOneOfRefs();

        if($this->type == 'array'){
            $result = $this->checkArrayPropertyRef(['items' => $this->items]);
            $this->items = $result['items'];
        }

        $this->properties = $this->checkPropertiesForRefs($this->properties);
    }

    /**
     * Проверяем поле oneOf на предмет ссылок
     *
     * @return void
     */
    private function checkOneOfRefs()
    {
        if(!empty($this->oneOf)){
            foreach($this->oneOf as &$oneOfProperties){
                $oneOfPropertiesProperties = $oneOfProperties['properties'] ?? [];
                $oneOfPropertiesProperties = $this->checkPropertiesForRefs($oneOfPropertiesProperties);
                $oneOfProperties['properties'] = $oneOfPropertiesProperties;
            }
        }
    }

    /**
     * Разбираем описание массива на предмет ссылок
     *
     * @param array $property
     * @return array
     */
    private function checkArrayPropertyRef(array $property) : array
    {
        if(isset($property['items']['ref'])){
            $this->usedRefs[] = $property['items']['ref'];
            $property['items']['$ref'] = '#/definitions/' . $property['items']['ref'];
            unset($property['items']['ref']);
        }

        return $property;
    }

    /**
     * Разбираем описание объекта
     *
     * @param array $property
     * @return array
     */
    private function checkObjectPropertyRef(array $property) : array
    {
        if(isset($property['ref'])){
            $this->usedRefs[] = $property['ref'];
            $property['$ref'] = '#/definitions/' . $property['ref'];
            unset($property['ref']);
        } else if(isset($property['properties'])){
            $propertyProperties = $this->checkPropertiesForRefs($property['properties']);
            
            if(!empty($propertyProperties)){
                $property['properties'] = $propertyProperties;
            }
        }
        return $property;
    }
    
    /**
     * Проверяем properties рекурсивно (в случае object) на наличие ссылок
     *
     * @param array $properties
     * @return array
     */
    private function checkPropertiesForRefs(array $properties) : array
    {
        
        foreach($properties as &$property){
            if($property['type'] == 'object'){
                $property = $this->checkObjectPropertyRef($property);
            }

            if($property['type'] == 'array'){
                $property = $this->checkArrayPropertyRef($property);
            }
        }

        return $properties;
    }

    /**
     * Получение массива всех ссылок рекурсивно
     *
     * @return void
     */
    public function getAllDefinitionRefs(){
        $definitionRefs = $this->usedRefs;
        foreach($this->usedRefs as $ref){
            $definition = $this->definitionsManager->getDefinitionByName($ref);
            if($definition != null){
                $definitionRefs = array_merge($definitionRefs, $definition->getAllDefinitionRefs());
            }
        }
        return $definitionRefs;
    }


    public function getSmdInfo() : array
    {
        $smdInfo = [
            'type' => $this->type
        ];

        if(!empty($this->items)){
            $smdInfo['items'] = $this->items;
        }

        if(!empty($this->properties)){
            $smdInfo['properties'] = $this->properties;
        }

        if(!empty($this->oneOf)){
            $smdInfo['oneOf'] = $this->oneOf;
        }

        return $smdInfo;
    }
}