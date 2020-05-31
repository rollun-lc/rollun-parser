<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SimpleTestTwoHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

class SimpleTestTwoHttpClientMiddlewareTest extends TestCase
{

    public function getObject($mockHandler)
    {
        $middleware = new SimpleTestTwoHttpClientMiddleware();
        $middleware->setHandler($mockHandler);
        return $middleware;
    }

    public function testMiddlewareWithOnFullfilled()
    {
        $expectedLog = [
            'SimpleTestHttpClientMiddleware onRequest',
            'SimpleTestHttpClientMiddleware onFullfilled',
        ];
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = MockHandler::createWithMiddleware([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $resp = $httpClient->send($request, []);

        $log = $resp->getHeader('middleware_log');

        $this->assertEquals($expectedLog, $log);
    }

    public function testUnserialize() {
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $this->assertEquals($handlerStack, unserialize(serialize($handlerStack)));
    }
}