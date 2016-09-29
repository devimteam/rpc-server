<?php

use Devimteam\Component\RpcServer\ResponseBuilder;
use Devimteam\Component\RpcServer\Exception\RpcParseException;

/**
 * Class ResponseBuilderTest
 */
class ResponseBuilderTest extends \Codeception\Test\Unit
{

    /**
     * @return array
     */
    public function builderDataProvider()
    {
        return [
            [
                ['123', ['t' => 1]],
                ['jsonrpc' => '2.0', 'id' => '123', 'result' => ['t' => 1]]
            ],
            [
                ['-10', null],
                ['jsonrpc' => '2.0', 'id' => '-10', 'result' => null]
            ],
            [
                [0, new RpcParseException()],
                ['jsonrpc' => '2.0', 'id' => 0, 'error' => ['code' => RpcParseException::PARSE_ERROR, 'message' => 'Parse error']]
            ],
        ];
    }

    /**
     * @dataProvider builderDataProvider
     *
     * @param $data
     * @param $result
     */
    public function testBuild($data, $result)
    {
        $builded = ResponseBuilder::build($data[0], $data[1]);
        $this->assertEquals($builded, $result, "::build() builds valid php array for JSON RPC 2.0 Specification");
    }
}