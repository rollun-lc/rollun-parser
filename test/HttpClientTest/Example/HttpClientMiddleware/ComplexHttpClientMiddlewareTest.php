<?php


namespace HttpClentTest\HttpClientMiddleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\HttpClientMiddleware\SimpleProxyHttpClientMiddleware;
use HttpClient\HttpClientMiddleware\CookieHttpClientMiddleware;
use HttpClient\HttpClientMiddleware\ResponseValidatorHttpClientMiddleware;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonBotDetectionResponseValidator;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonCaptchaResponseValidator;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonZipCodeResponseValidator;
use PHPUnit\Framework\TestCase;
use rollun\datastore\DataStore\Memory;
use HttpClient\HttpResponseValidator\StatusOk;
use function GuzzleHttp\choose_handler;

class CpmplexHttpClientMiddlewareTest extends TestCase
{

    /**
     * @return Closure
     */
    protected function getProxyMiddlewareFactoryFunction()
    {
        return function ($nextHandler) {
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
            $middleware->setHandler($nextHandler);
            return $middleware;
        };
    }

    protected function getCookieMiddlewareFactoryFunction()
    {
        return function ($nextHandler) {
            $middleware = new CookieHttpClientMiddleware(new CookieJar());
            $middleware->setHandler($nextHandler);
            return $middleware;
        };
    }

    protected function getResponseValidatorMiddlewareFactoryFunction()
    {
        return function ($nextHandler) {

            $responseValidators = [
                new AmazonZipCodeResponseValidator('77632'),
                new AmazonCaptchaResponseValidator(),
                new AmazonBotDetectionResponseValidator()
            ];
            $middleware = new ResponseValidatorHttpClientMiddleware($responseValidators);
            $middleware->setHandler($nextHandler);
            return $middleware;
        };
    }

    protected function getHandlerStack($handler)
    {
        $middlewares = [
            $this->getProxyMiddlewareFactoryFunction(),
            $this->getCookieMiddlewareFactoryFunction(),
            $this->getResponseValidatorMiddlewareFactoryFunction(),
        ];
        $stack = new HandlerStack($handler);

        foreach ($middlewares as $index => $middlewareFactoryFunction) {
            $stack->push($middlewareFactoryFunction, 'test_middleware_' . $index);
        }

        return $stack;
    }

    public function testWithSuccessfulResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([new Response(200)]);
        $middlewares = [
            $this->getProxyMiddlewareFactoryFunction(),
            $this->getCookieMiddlewareFactoryFunction(),
            function ($nextHandler) {
                $responseValidators = [
                    new StatusOk(),
                ];
                $middleware = new ResponseValidatorHttpClientMiddleware($responseValidators);
                $middleware->setHandler($nextHandler);
                return $middleware;
            },
        ];
        $handlerStack = new HandlerStack($handler);

        foreach ($middlewares as $index => $middlewareFactoryFunction) {
            $handlerStack->push($middlewareFactoryFunction, 'test_middleware_' . $index);
        }
        $httpClient = new Client(['handler' => $handlerStack]);
        $resp = $httpClient->send($request, []);

        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @todo rewrite
     */
    public function testWithFailedResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([new Response(500)]);
        $handlerStack = $this->getHandlerStack($handler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $promise = $httpClient->sendAsync($request, []);
        $response = $promise->wait(false);
        $this->assertTrue(true);
    }

    public function testWithBadProxy()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = choose_handler();
        $handlerStack = $this->getHandlerStack($handler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->expectException(ConnectException::class);
        $promise = $httpClient->sendAsync($request, []);
        $response = $promise->wait();
        $this->assertTrue(true);
    }

    public function testWithCookieFailedResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([new Response(200, [], '')]);
        $handlerStack = $this->getHandlerStack($handler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $promise = $httpClient->sendAsync($request, []);
        $cookieId = null;
        try {
            $response = $promise->wait();
        } catch (\Exception $e) {
            $handlerContext = $e->getHandlerContext();
            $cookieId = $handlerContext['cookie_id'];
        }

        $this->assertEquals('testCookieId', $cookieId);
    }

    public function testWithCaptchaFoundResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([new Response(200, [], 'Type the characters you see in this image')]);
        $middlewares = [
            $this->getProxyMiddlewareFactoryFunction(),
            $this->getCookieMiddlewareFactoryFunction(),
            function ($nextHandler) {
                $responseValidators = [
                    new StatusOk(),
                    new AmazonCaptchaResponseValidator(),
                    new AmazonBotDetectionResponseValidator()
                ];
                $middleware = new ResponseValidatorHttpClientMiddleware($responseValidators);
                $middleware->setHandler($nextHandler);
                return $middleware;
            },
        ];
        $handlerStack = new HandlerStack($handler);

        foreach ($middlewares as $index => $middlewareFactoryFunction) {
            $handlerStack->push($middlewareFactoryFunction, 'test_middleware_' . $index);
        }

        $httpClient = new Client(['handler' => $handlerStack]);

        $promise = $httpClient->sendAsync($request, []);
        $proxyId = null;
        try {
            $response = $promise->wait();
        } catch (\Exception $e) {
            $handlerContext = $e->getHandlerContext();
            $proxyId = $handlerContext['proxy_id'];
        }

        $this->assertEquals('proxyId', $proxyId);
    }

    public function testWithBotDetectedResponse()
    {
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $handler = new MockHandler([
            new Response(200, [], 'To discuss automated access to Amazon data please contact')
        ]);
        $middlewares = [
            $this->getProxyMiddlewareFactoryFunction(),
            $this->getCookieMiddlewareFactoryFunction(),
            function ($nextHandler) {
                $responseValidators = [
                    new StatusOk(),
                    new AmazonCaptchaResponseValidator(),
                    new AmazonBotDetectionResponseValidator()
                ];
                $middleware = new ResponseValidatorHttpClientMiddleware($responseValidators);
                $middleware->setHandler($nextHandler);
                return $middleware;
            },
        ];
        $handlerStack = new HandlerStack($handler);

        foreach ($middlewares as $index => $middlewareFactoryFunction) {
            $handlerStack->push($middlewareFactoryFunction, 'test_middleware_' . $index);
        }

        $httpClient = new Client(['handler' => $handlerStack]);

        $promise = $httpClient->sendAsync($request, []);
        $proxyId = null;
        try {
            $response = $promise->wait();
        } catch (\Exception $e) {
            $handlerContext = $e->getHandlerContext();
            $proxyId = $handlerContext['proxy_id'];
        }

        $this->assertEquals('proxyId', $proxyId);
    }

    public function testTest()
    {
        $zipCode = '77632';
        $html = '';
        $res = stripos($html, "Orange {$zipCode}");
        $this->assertEquals(false, $res);
    }
}