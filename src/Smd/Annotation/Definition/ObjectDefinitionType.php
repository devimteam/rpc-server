<?php

namespace Devim\Component\RpcServer\Smd\Annotation\Definition;

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
    public function getSmdInfo(): array
    {
        $info = parent::getSmdInfo();

        $hasRef = !empty($this->ref);
        $hasProperties = !empty($this->properties);

        if ($hasRef && $hasProperties) {
            throw new Exception\SmdInvalidDefinition('Object definition may have "properties" or "ref" attributes, but not both');
        }

        if ($hasRef) {
            $info['$ref'] = '#/definitions/' . $this->ref;
        } else if ($hasProperties) {
            $info['properties'] = $this->getSmdProperties();
        } else {
            throw new Exception\SmdInvalidDefinition('Object definition must have either "properties" or "ref" attribute');
        }

        return $info;
    }

    /**
     * @return array
     */
    private function getSmdProperties(): array
    {
        $result = [];
        foreach ($this->properties as $item) {
            $result[$item->name] = $item->getSmdInfo();
        }
        return $result;
    }
}
