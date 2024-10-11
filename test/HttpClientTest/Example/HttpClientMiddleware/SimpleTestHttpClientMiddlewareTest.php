<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SimpleTestHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

class SimpleTestHttpClientMiddlewareTest extends TestCase
{

    public function getObject($mockHandler)
    {
        $middleware = new SimpleTestHttpClientMiddleware();
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
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $promise = $httpClient->sendAsync($request, []);
        $response = $promise->wait();
        $log = $response->getHeader('middleware_log');

        $this->assertEquals($expectedLog, $log);
    }

    public function testUnserialize()
    {
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $this->assertEquals($handlerStack, unserialize(serialize($handlerStack)));
    }
}