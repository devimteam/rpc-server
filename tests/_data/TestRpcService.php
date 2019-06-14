<?php

use Devim\Component\RpcServer\Smd\Annotation\{
    Service,
    Parameters,
    Errors,
    Error
};
use Devim\Component\RpcServer\Smd\Annotation\Parameter\{
    ArrayParameterType as ArrParam,
    BooleanParameterType as BoolParam,
    IntegerParameterType as IntParam,
    NumberParameterType as NumParam,
    ObjectParameterType as ObjParam,
    StringParameterType as StrParam
};
use Devim\Component\RpcServer\Smd\Annotation\Definition\{
    ArrayDefinitionType as ArrProp,
    BooleanDefinitionType as BoolProp,
    IntegerDefinitionType as IntProp,
    NumberDefinitionType as NumProp,
    ObjectDefinitionType as ObjProp,
    StringDefinitionType as StrProp
};

/**
 * Class TestRpcService
 */
class TestRpcService
{
    /**
     * @Service(
     *     description="Описание сервиса", 
     *     parameters=@Parameters(
     *         items={
     *             @ArrParam(name="array_prop", description="Array property", type="boolean"),
     *             @ArrParam(name="typed_array_prop", description="Typed array property", type="some_another_type",
     *                 definitions={
     *                     @ObjProp(name="some_type", description="Some type description", properties={
     *                         @StrProp(name="prop1", description="Property 1"),
     *                         @StrProp(name="prop2", description="Property 2"),
     *                     }),
     *                     @ObjProp(name="some_another_type", description="Some another type description", properties={
     *                         @ArrProp(name="array_prop", description="Array property", type="object"),
     *                         @BoolProp(name="boolean_prop", description="Boolean property"),
     *                         @IntProp(name="integer_prop", description="Integer property"),
     *                         @NumProp(name="number_prop", description="Number property"),
     *                         @ObjProp(name="some_type_filed", description="Some type description", ref="some_type"),
     *                         @StrProp(name="string_prop", description="String property"),
     *                     }),
     *                 }
     *             ),
     *             @BoolParam(name="boolean_prop", description="Boolean property"),
     *             @IntParam(name="integer_prop", description="Integer property"),
     *             @NumParam(name="number_prop", description="Number property"),
     *             @ObjParam(name="some_type_filed", description="Some type description", ref="some_type",
     *                 definitions={
     *                     @ObjProp(name="some_type", description="Some type description", properties={
     *                         @StrProp(name="prop1", description="Property 1"),
     *                         @StrProp(name="prop2", description="Property 2"),
     *                     }),
     *                 }
     *             ),
     *             @StrParam(name="string_prop", description="String property"),
     *         },
     *     ),
     *     returns=@ObjParam(name="data", description="Test object return parameter", ref="some_type",
     *         definitions={
     *             @ObjProp(name="some_type", description="Some type description", properties={
     *                 @StrProp(name="prop1", description="Property 1"),
     *                 @StrProp(name="prop2", description="Property 2"),
     *             }),
     *             @ObjProp(name="some_another_type", description="Some another type description", properties={
     *                 @ArrProp(name="array_prop", description="Array property", type="some_another_type"),
     *                 @BoolProp(name="boolean_prop", description="Boolean property"),
     *                 @IntProp(name="integer_prop", description="Integer property"),
     *                 @NumProp(name="number_prop", description="Number property"),
     *                 @ObjProp(name="some_type_filed", description="Some type description", ref="some_type"),
     *                 @StrProp(name="string_prop", description="String property"),
     *             }),
     *         }
     *     ),
     *     errors=@Errors({
     *         @Error("123", description="Error 123"),
     *         @Error("321", description="Error 321"),
     *     }),
     * )
     * 
     * @param $value
     *
     * @return array
     */
    public function method($value)
    {
        return [$value];
    }

}
