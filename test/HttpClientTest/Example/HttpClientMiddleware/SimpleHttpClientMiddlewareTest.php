<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SimpleHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

class SimpleHttpClientMiddlewareTest extends TestCase
{

    public function getObject($mockHandler)
    {
        $middleware = new SimpleHttpClientMiddleware();
        $middleware->setHandler($mockHandler);
        return $middleware;
    }

    public function testMiddleware()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = MockHandler::createWithMiddleware([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $resp = $httpClient->send($request, []);

        $this->assertEquals('testValue', $resp->getHeader('response_header')[0]);
    }

    public function testUnserialize() {
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $this->assertEquals($handlerStack, unserialize(serialize($handlerStack)));
    }
}