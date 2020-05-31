<?php


namespace HttpClientTest\Example\Loader\Example1;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\Example\Loader\Example1\SimpleLoader;
use HttpClient\Example\Loader\Example1\UsageExample;
use PHPUnit\Framework\TestCase;

class SimpleLoaderTest extends TestCase
{
    public function testCreate()
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
        $this->assertInstanceOf(SimpleLoader::class, $loader);
        $this->assertEquals('test body', $html);
    }

    public function testUsage()
    {
        $html = UsageExample::example1();
        $this->assertEquals('test body', $html);
    }
}