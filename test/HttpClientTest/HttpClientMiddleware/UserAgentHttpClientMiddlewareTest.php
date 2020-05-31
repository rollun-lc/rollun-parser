<?php


namespace HttpClientTest\HttpClientMiddleware;


use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use HttpClient\HttpClientMiddleware\UserAgentHttpClientMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Class UserAgentHttpClientMiddlewareTest
 *
 * @package HttpClientTest\Middleware
 */
class UserAgentHttpClientMiddlewareTest extends TestCase
{
    public function testCreate()
    {
        $object = new UserAgentHttpClientMiddleware(
            'test-user-agent'
        );
        $object->setHandler(new MockHandler([new Response(200)]));
        $this->assertInstanceOf(UserAgentHttpClientMiddleware::class, $object);
    }
}