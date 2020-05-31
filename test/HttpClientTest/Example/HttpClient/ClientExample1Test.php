<?php


namespace HttpClientTest\Example\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HttpClient\Example\HttpClient\ClientExample1;
use PHPUnit\Framework\TestCase;

class ClientExample1Test extends TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCreateWithMockHandlerOnly()
    {
        $clientExample1 = new ClientExample1();
        $httpClient = $clientExample1->createClientWithMockHandlerOnly();
        $httpClientConfig = $httpClient->getConfig();
        $this->assertInstanceOf(Client::class, $httpClient);
        $this->assertInstanceOf(MockHandler::class, $httpClientConfig['handler']);

        $request = new Request('GET', 'localhost');
        $response = $httpClient->send($request);
        $this->assertEquals($response, new Response(200));
    }

    public function testCreateWithStandartHandlers()
    {
        $clientExample1 = new ClientExample1();
        $httpClient = $clientExample1->createClientWithStandartHandlers();
        $httpClientConfig = $httpClient->getConfig();
        $handlerStack =  $httpClientConfig['handler'];
        $this->assertInstanceOf(Client::class, $httpClient);
        $this->assertInstanceOf(HandlerStack::class, $handlerStack);
    }

}