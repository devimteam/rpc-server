<?php
namespace Devim\Component\RpcServer\Smd\Annotation\Definitions;

use Devim\Component\RpcServer\Smd\Exception\SmdInvalidDefinitionRef;

class DefinitionsManager
{
    /**
     * @var array<Definition>
     */
    public $allDefinitions;

    public function __construct(string $jsonDefinitions)
    {
        $definitionsArray = json_decode($jsonDefinitions, true);
        foreach($definitionsArray as $name => $definition){
            $definitionObject = new Definition($this);
            $definition['name'] = $name;

            $definitionObject->parseFromJsonArray($definition);
            $this->allDefinitions[$name] =  $definitionObject;
        }

    }

    /**
     * @param string $name
     * @return Definition
     */
    public function getDefinitionByName(string $name) : Definition
    {
        if(!isset($this->allDefinitions[$name])){
            throw new SmdInvalidDefinitionRef($name);
        }

        return $this->allDefinitions[$name];
    }

    /**
     * Получаем информацию в формате SMD для всех используемых definitions по имени
     *
     * @param string $name
     * @return array
     */
    public function resolveSmdDefinitionsByName(string $name) : array
    {
        $rootDefinition = $this->getDefinitionByName($name);
        $resolvedSmdDefinitions = [];

        $resolvedSmdDefinitions[$rootDefinition->name] = $rootDefinition->getSmdInfo();
        $rootDefinitionUsedRefs = $rootDefinition->getAllDefinitionRefs();
        
        foreach($rootDefinitionUsedRefs as $ref){
            $usedDefinition = $this->getDefinitionByName($ref);
            $resolvedSmdDefinitions[$usedDefinition->name] = $usedDefinition->getSmdInfo();
        }

        return $resolvedSmdDefinitions;
    }
}