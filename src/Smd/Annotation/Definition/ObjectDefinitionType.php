<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

use Devim\Component\RpcServer\Smd\Annotation\Service;
use Doctrine\Common\Annotations\Annotation\Required;
use Devim\Component\RpcServer\Smd\Exception;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ObjectDefinitionType extends AbstractDefinitionType
{

    /**
     * @var array<Devim\Component\RpcServer\Smd\Annotation\Definition\AbstractDefinitionType>
     */
    public $properties;

    /**
     * @var string
     */
    public $ref;

    /**
     * @return array
     * @throws Exception\SmdInvalidDefinition
     */
    public function getSmdInfo(Service $service): array
    {
        $info = parent::getSmdInfo($service);

        $hasRef = !empty($this->ref);
        $hasProperties = !empty($this->properties);

        if ($hasRef && $hasProperties) {
            throw new Exception\SmdInvalidDefinition('Object definition may have "properties" or "ref" attributes, but not both');
        }

        if ($hasRef) {
            $info['$ref'] = '#/definitions/' . $this->ref;
            
        } else if ($hasProperties) {
            $info['properties'] = $this->getSmdProperties($service);

        } else {
            throw new Exception\SmdInvalidDefinition('Object definition must have either "properties" or "ref" attribute');
        }

        return $info;
    }

    /**
     * @return array
     */
    private function getSmdProperties(Service $service): array
    {
        $result = [];
        foreach ($this->properties as $item) {
            $result[$item->name] = $item->getSmdInfo($service);
        }
        return $result;
    }
}
