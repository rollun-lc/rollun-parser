<?php


namespace HttpClient\Example\Loader\Example2;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\HttpResponseValidator\StatusOk;

class UsageExample
{
    public static function example1()
    {
        // create handlerStack
        $mockHandler = new MockHandler([
            new Response(200, [], 'test body'),
            new Response(200, [], 'test body')
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        // create httpClient
        $httpClient = new Client(['handler' => $handlerStack]);

        $authenticationClient = new Client(['handler' => $handlerStack]);
        $authenticationFormData = [
            'login' => 'your@login.test',
            'password' => 'pasword',
        ];
        $authenticationRequest = new ServerRequest('POST', '', [], http_build_query($authenticationFormData, '', '&'));

        $responsevalidator = new StatusOk();
        // create Loader
        $loader = new LoaderWithAuthentication(
            $httpClient,
            $authenticationClient,
            $authenticationRequest,
            $responsevalidator
        );

        // usage
        $request = new ServerRequest('GET', 'http://google.com');
        $html = $loader->__invoke($request);
        return $html;
    }
}