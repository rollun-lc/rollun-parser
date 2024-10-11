<?php


namespace HttpClientTest\Example\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HttpClient\Example\HttpClient\ClientExample2;
use HttpClient\Example\HttpClient\ClientExample3;
use PHPUnit\Framework\TestCase;

class ClientExample3Test extends TestCase
{
    public function testCreateClientWithResponseValidatorHandler()
    {
        $clientExample1 = new ClientExample3();
        $httpClient = $clientExample1->createClientWithResponseValidatorHandler();

        $exceptionMessage = null;
        $request = new Request('GET', 'localhost');
        try {
            $response = $httpClient->send($request);
        } catch (\Exception $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertEquals('Reason phrase is Internal Server Error', $exceptionMessage);
    }

    public function testCreateClientWithRollunResponseValidatorHandler()
    {
        $clientExample1 = new ClientExample3();
        $httpClient = $clientExample1->createClientWithRollunResponseValidatorHandler();

        $exceptionMessage = null;
        $request1 = new Request('GET', 'http://rollun.com/');
        $request2 = new Request('GET', 'http://rollun.com/contacts/wrong_page');

        $response1 = $httpClient->send($request1);
        $this->assertEquals(200, $response1->getStatusCode());

        try {
            $response2 = $httpClient->send($request2);
        } catch (\Exception $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertEquals('Page not found', $exceptionMessage);
    }

}