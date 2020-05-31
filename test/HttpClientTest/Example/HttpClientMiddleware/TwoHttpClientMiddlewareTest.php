<?php


namespace HttpClentTest\HttpClientMiddleware;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\AmazonProxyHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\FirstHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\SecondHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Class TwoHttpClientMiddlewareTest
 *
 * @package HttpClentTest
 */
class TwoHttpClientMiddlewareTest extends TestCase
{

    /**
     * Тест на очередность вызова middleware -
     *  при отправке запроса первым должен отрабатывать middleware который первым добавлен в стек
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testMiddleware1()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);

        $handler = new MockHandler([new Response(200)]);
        $handlerStack = new HandlerStack($handler);
        $handlerStack->push(FirstHttpClientMiddleware::getHttpClientMiddlewareFactoryFunction());
        $handlerStack->push(SecondHttpClientMiddleware::getHttpClientMiddlewareFactoryFunction());
        $httpClient = new Client(['handler' => $handlerStack]);
        $resp = $httpClient->send($request, []);
        $log = $resp->getHeader('middleware_log');
        $expectedLog = [
            'FirstHttpClientMiddleware - request',
            'SecondHttpClientMiddleware - request',
            'SecondHttpClientMiddleware - response',
            'FirstHttpClientMiddleware - response',
        ];
        $this->assertEquals($expectedLog, $log);
    }
}