<?php


namespace HttpClient\Example\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use HttpClient\Example\HttpClientMiddleware\FirstHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\SimpleHttpClientMiddleware;

/**
 * Class ClientExample1
 *
 * @package HttpClient\Example\HttpClient
 */
class ClientExample2
{
    /**
     *  returns HttpClient with FirstHttpClientMiddleware and MockHandler
     *
     * @return Client
     * @see src/HttpClient/src/Example/HttpClientMiddleware/FirstHttpClientMiddleware.php
     */
    public function createClientWithCustomHandler(): Client
    {
        $handler = new MockHandler([new Response(200)]);
        $handlerStack = new HandlerStack($handler);
        $handlerStack->push(function ($nextHandler) {
            $middleware = new FirstHttpClientMiddleware();
            $middleware->setHandler($nextHandler);
            return $middleware;
        });
        return new Client(['handler' => $handlerStack]);
    }

    /**
     *  returns HttpClient with handlerStack `TestAmazonHandlerStackCustomServiceName` and MockHandler
     *  handlerStack `TestAmazonHandlerStackCustomServiceName` contains middlewares:
     *      - 'user_agent' => 'EmptyUserAgentHttpClientMiddleware',
     *      - 'simple' => SimpleHttpClientMiddleware::class,
     *
     * @return Client
     * @see config/autoload/development.handlerstack.test.global.php
     */
    public function createClientWithHandlerStackfromContainer(): Client
    {
        global $container;
        $handler = new MockHandler([new Response(200)]);
        /**
         * service TestAmazonHandlerStackCustomServiceName
         * described at config/autoload/development.handlerstack.test.global.php
         */
        $handlerStack = $container->get('TestAmazonHandlerStackCustomServiceName');
        // change curl_handler to mock_handler
        $handlerStack->sethandler($handler);
        return new Client(['handler' => $handlerStack]);
    }
}
