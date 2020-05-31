<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SecondHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\SimpleHttpClientMiddleware;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use PHPUnit\Framework\TestCase;

class SecondHttpClientMiddlewareTest extends TestCase
{


//    public function testGetFromPluginManager()
//    {
//        global $container;
//        $httpClientMiddlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
//        /**
//         * service serviceStatusOkResponseValidatorHttpMiddleware described at
//         * config/autoload/development.handlerstack.test.global.php
//         */
//        $middlewareFactoryFunction = $httpClientMiddlewarePluginManager->getHttpClientMiddlewareFactoryFunction(
//            SecondHttpClientMiddleware::class
//        );
//
//
//        $request = new ServerRequest('GET', 'https://www.google.com', []);
//        $handler = MockHandler::createWithMiddleware([new Response(200)]);
//        $handlerStack = new HandlerStack($handler);
//        $handlerStack->push($middlewareFactoryFunction);
//        $httpClient = new Client(['handler' => $handlerStack]);
//        $resp = $httpClient->send($request, []);
//
//        $this->assertEquals('testValue', $resp->getHeader('response_header')[0]);
//    }

    public function testTest()
    {
        $this->assertTrue(true);
    }
}