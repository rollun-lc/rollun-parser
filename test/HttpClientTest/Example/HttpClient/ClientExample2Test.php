<?php


namespace HttpClientTest\Example\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HttpClient\Example\HttpClient\ClientExample2;
use PHPUnit\Framework\TestCase;

class ClientExample2Test extends TestCase
{
    public function testCreateClientWithCustomHandler()
    {
        $clientExample1 = new ClientExample2();
        $httpClient = $clientExample1->createClientWithCustomHandler();
        $httpClientConfig = $httpClient->getConfig();


        $request = new Request('GET', 'localhost');
        $response = $httpClient->send($request);

        $this->assertInstanceOf(Client::class, $httpClient);
        $this->assertInstanceOf(HandlerStack::class, $httpClientConfig['handler']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'FirstHttpClientMiddleware - request',
            'FirstHttpClientMiddleware - response',
        ], $response->getHeader('middleware_log'));
    }

    public function testCreateClientWithHandlerStackfromContainer()
    {
        $clientExample1 = new ClientExample2();
        $httpClient = $clientExample1->createClientWithHandlerStackfromContainer();
        $httpClientConfig = $httpClient->getConfig();

        $request = new Request('GET', 'localhost');
        $response = $httpClient->send($request);

        $this->assertInstanceOf(Client::class, $httpClient);
        $this->assertInstanceOf(HandlerStack::class, $httpClientConfig['handler']);
        $this->assertEquals(200, $response->getStatusCode());

    }

}