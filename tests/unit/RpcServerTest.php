<?php

use Devim\Component\RpcServer\RpcServer;
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
        $rpcServer = new RpcServer();
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

