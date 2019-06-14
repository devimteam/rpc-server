<?php

use Devim\Component\RpcServer\RpcServer;
use Devim\Component\RpcServer\Smd\SmdGenerator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RpcServerTest extends \Codeception\Test\Unit
{

    /**
     * @link http://www.jsonrpc.org/specification
     * @return array
     */
    public function specDataProvider()
    {
        $specTests = [
            [
                '{"jsonrpc": "2.0", "method": "test.method", "params": [1], "id": "3"}',
                '{"jsonrpc": "2.0", "result": [1], "id": "3"}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "test.method", "params": {"value": 5}, "id": "3"}',
                '{"jsonrpc": "2.0", "result": [5], "id": "3"}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "math.subtract", "params": [56, 51], "id": 1}',
                '{"jsonrpc": "2.0", "error": {"code":-32601, "message":"Service \u0022math\u0022 not found"}, "id": 1}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "math.subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 1}',
                '{"jsonrpc": "2.0", "error": {"code":-32601, "message":"Service \u0022math\u0022 not found"}, "id": 1}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "math.subtract", "params": [42, 23]}',
                '{"jsonrpc": "2.0", "error": {"code":-32601, "message":"Service \u0022math\u0022 not found"}, "id": null}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "test.subtract", "params": [42, 23]}',
                '{"jsonrpc": "2.0", "error": {"code":-32601,"message":"Method \u0022subtract\u0022 not found in service \u0022test\u0022"}, "id": null}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "math.subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 1]',
                '{"jsonrpc": "2.0", "error": {"code":-32700,"message":"Parse error"}, "id": null}',
            ],
            [
                '{"jsonrpc": "2.0", "method": 1, "params": "subtrahend", "id": 1]',
                '{"jsonrpc": "2.0", "error": {"code":-32700,"message":"Parse error"}, "id": null}',
            ],
            [
                '[{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},{"jsonrpc": "2.0", "method"]',
                '{"jsonrpc": "2.0", "error": {"code":-32700,"message":"Parse error"}, "id": null}',
            ],
            [
                '[]',
                '{"jsonrpc": "2.0", "error": {"code":-32600,"message":"Invalid Request"}, "id": null}',
            ],
            [
                '[1]',
                '{"jsonrpc": "2.0", "error": {"code":-32600,"message":"Invalid Request"}, "id": null}',
            ],
            [
                '[
                    {"jsonrpc": "2.0", "method": "test.method", "params": [1], "id": "3"},
                    {"jsonrpc": "2.0", "method": "test.notExistsMethod", "params": [1], "id": "3"},
                    {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
                    {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]},
                    {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},
                    {"foo": "boo"},
                    {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},
                    {"jsonrpc": "2.0", "method": "get_data", "id": "9"}
                ]',
                '[
                    {"jsonrpc": "2.0", "result": [1], "id": "3"},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method \u0022notExistsMethod\u0022 not found in service \u0022test\u0022"}, "id":"3"},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Service \u0022\u0022 not found"}, "id":"1"},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Service \u0022\u0022 not found"}, "id":null},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Service \u0022\u0022 not found"}, "id":"2"},
                    {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id":null},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Service \u0022foo\u0022 not found"}, "id":"5"},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Service \u0022\u0022 not found"}, "id":"9"}
                 ]',
            ],
            [
                '[
                    {"jsonrpc": "2.0", "method": "test.method", "params": {"value":2}, "id": 1 },
                    {"jsonrpc": "2.0", "method": "test.notExistsMethod", "params": [7], "id": 2}
                ]',
                '[
                    {"jsonrpc": "2.0", "result": [2], "id": 1},
                    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method \u0022notExistsMethod\u0022 not found in service \u0022test\u0022"}, "id": 2}
                 ]',
            ],
        ];

        foreach ($specTests as &$test) {
            $test[0] = new Request([], [], [], [], [], [], $test[0]);
            $test[1] = new JsonResponse(json_decode($test[1]));
        }

        $specTests[] = [
            new Request(['smd' => null], [], [], [], [], [], []),
            new JsonResponse(json_decode('{"transport":"POST","envelope":"JSON-RPC-2.0","contentType":"application\/json","SMDVersion":"2.0","target":"\/","services":{"test.method":{"description":"\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435 \u0441\u0435\u0440\u0432\u0438\u0441\u0430","parameters":[{"type":"array","name":"array_prop","description":"Array property","items":{"type":"boolean"}},{"type":"array","name":"typed_array_prop","description":"Typed array property","definitions":{"some_type":{"type":"object","name":"some_type","description":"Some type description","properties":{"prop1":{"type":"string","name":"prop1","description":"Property 1"},"prop2":{"type":"string","name":"prop2","description":"Property 2"}}},"some_another_type":{"type":"object","name":"some_another_type","description":"Some another type description","properties":{"array_prop":{"type":"array","name":"array_prop","description":"Array property","items":{"type":"object"}},"boolean_prop":{"type":"boolean","name":"boolean_prop","description":"Boolean property"},"integer_prop":{"type":"integer","name":"integer_prop","description":"Integer property"},"number_prop":{"type":"number","name":"number_prop","description":"Number property"},"some_type_filed":{"type":"object","name":"some_type_filed","description":"Some type description","$ref":"#\/definitions\/some_type"},"string_prop":{"type":"string","name":"string_prop","description":"String property"}}}},"items":{"$ref":"#\/definitions\/some_another_type"}},{"type":"boolean","name":"boolean_prop","description":"Boolean property"},{"type":"integer","name":"integer_prop","description":"Integer property"},{"type":"number","name":"number_prop","description":"Number property"},{"type":"object","name":"some_type_filed","description":"Some type description","definitions":{"some_type":{"type":"object","name":"some_type","description":"Some type description","properties":{"prop1":{"type":"string","name":"prop1","description":"Property 1"},"prop2":{"type":"string","name":"prop2","description":"Property 2"}}}},"$ref":"#\/definitions\/some_type"},{"type":"string","name":"string_prop","description":"String property"}],"returns":{"type":"object","name":"data","description":"Test object return parameter","definitions":{"some_type":{"type":"object","name":"some_type","description":"Some type description","properties":{"prop1":{"type":"string","name":"prop1","description":"Property 1"},"prop2":{"type":"string","name":"prop2","description":"Property 2"}}},"some_another_type":{"type":"object","name":"some_another_type","description":"Some another type description","properties":{"array_prop":{"type":"array","name":"array_prop","description":"Array property","items":{"$ref":"#\/definitions\/some_another_type"}},"boolean_prop":{"type":"boolean","name":"boolean_prop","description":"Boolean property"},"integer_prop":{"type":"integer","name":"integer_prop","description":"Integer property"},"number_prop":{"type":"number","name":"number_prop","description":"Number property"},"some_type_filed":{"type":"object","name":"some_type_filed","description":"Some type description","$ref":"#\/definitions\/some_type"},"string_prop":{"type":"string","name":"string_prop","description":"String property"}}}},"$ref":"#\/definitions\/some_type"},"errors":{"123":"Error 123","321":"Error 321"}}}}')),
        ];
        
        return $specTests;
    }

    /**
     * @dataProvider specDataProvider
     *
     * @param $request
     * @param $response
     */
    public function testRunSpec($request, $response)
    {
        $rpcServer = new RpcServer(
            new Doctrine\Common\Annotations\AnnotationReader, 
            new SmdGenerator('/')
        );
        $rpcServer->addService(TestRpcService::class, function () {
            return [];
        });
        $this->assertEquals(
            $rpcServer->run($request),
            $response,
            '->run() properly process JSON RPC 2.0 request and achive spec requirements'
        );
    }
}

