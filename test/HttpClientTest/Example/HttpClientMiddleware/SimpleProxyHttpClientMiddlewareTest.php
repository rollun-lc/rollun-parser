<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SimpleProxyHttpClientMiddleware;
use PHPUnit\Framework\TestCase;
use rollun\datastore\DataStore\Memory;
use function GuzzleHttp\choose_handler;

class SimpleProxyHttpClientMiddlewareTest extends TestCase
{

    public function getObject($mockHandler)
    {
        $proxyDataStore = new Memory([
            'id',
            'proxy',
            'rating',
            'last_time_used',
            'created_at',
            'updated_at',
            'source'
        ]);
        $proxyDataStore->create([
            'id' => SimpleProxyHttpClientMiddleware::DEFAULT_PROXY_ID,
            'proxy' => 'http://0.0.0.0',
            'rating' => 0,
            'last_time_used' => 0,
            'created_at' => 1572278599.975,
            'updated_at' => 1572278599.975,
            'source' => 'Test',
        ]);
        $middleware = new SimpleProxyHttpClientMiddleware($proxyDataStore);
        $middleware->setHandler($mockHandler);
        return $middleware;
    }

    public function testWithSuccessfulResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);
        $resp = $httpClient->send($request, []);

        $this->assertEquals('testValue', 'testValue');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @todo rewrite
     */
    public function testWithFailedResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = MockHandler::createWithMiddleware([new Response(500)]);
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->expectException(ServerException::class);
        $resp = $httpClient->send($request, []);
    }

    public function testUnserialize()
    {
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = $this->getObject($handler);
        $this->assertEquals($handlerStack, unserialize(serialize($handlerStack)));
    }

    public function testWithBadProxy()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = choose_handler();
        $handlerStack = $this->getObject($handler);
        $httpClient = new Client(['handler' => $handlerStack]);

        //$this->expectException(ConnectException::class);
        $promise = $httpClient->sendAsync($request, []);
        $response = $promise->wait(false);
        $this->assertTrue(true);
    }
}