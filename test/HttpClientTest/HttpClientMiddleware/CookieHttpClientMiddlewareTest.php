<?php

namespace HttpClientTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\HttpClientMiddleware\CookieHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Class UserAgentHttpClientMiddlewareTest
 *
 * @package HttpClientTest\Middleware
 */
class CookieHttpClientMiddlewareTest extends TestCase
{
    public function testCreate()
    {
        $cookieJar = new CookieJar();
        $object = new CookieHttpClientMiddleware($cookieJar);
        $object->setHandler(new MockHandler([new Response(200)]));
        $this->assertInstanceOf(CookieHttpClientMiddleware::class, $object);
    }

    public function testWithCookies()
    {
        $expectedCookieValue = [
            'Name' => 'test_name2',
            'Value' => 'test_value2',
        ];
        $cookieJar = new CookieJar();
        $object = new CookieHttpClientMiddleware($cookieJar);
        $testResponse = new Response(200, [
            'Set-Cookie' => [
                $expectedCookieValue['Name'] . '=' . $expectedCookieValue['Value']
            ]
        ]);
        $object->setHandler(new MockHandler([$testResponse]));
        $httpClient = new Client(['handler' => $object]);

        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $response = $httpClient->send($request, []);

        $cookieValue = [
            'Name' => $cookieJar->toArray()[0]['Name'],
            'Value' => $cookieJar->toArray()[0]['Value'],
        ];
        $this->assertEquals($expectedCookieValue, $cookieValue);
    }
}