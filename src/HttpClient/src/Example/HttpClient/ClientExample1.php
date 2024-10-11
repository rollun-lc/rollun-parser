<?php


namespace HttpClient\Example\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Class ClientExample1
 *
 * @package HttpClient\Example\HttpClient
 */
class ClientExample1
{
    /**
     *  returns HttpClient with single MockHandler()
     *
     * @return Client
     * @link http://docs.guzzlephp.org/en/stable/testing.html
     */
    public function createClientWithMockHandlerOnly(): Client
    {
        return new Client(['handler' => new MockHandler([new Response(200)])]);
    }

    /**
     * @see
     *  equivalent to return new Client();
     *  returns HttpClient with standart handlers
     *
     * @return Client
     * @link http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html
     */
    public function createClientWithStandartHandlers(): Client
    {
        return new Client(['handler' => HandlerStack::create()]);
    }



}