<?php


namespace HttpClient\Example\Loader\Example1;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

class UsageExample
{
    public static function example1()
    {
        // create handlerStack
        $mockHandler = new MockHandler([new Response(200, [], 'test body')]);
        $handlerStack = HandlerStack::create($mockHandler);

        // create httpClient
        $httpClient = new Client(['handler' => $handlerStack]);

        // create Loader
        $loader = new SimpleLoader($httpClient);

        // usage
        $request = new ServerRequest('GET', 'http://google.com');
        $html = $loader->__invoke($request);
        return $html;
    }
}