<?php


namespace HttpClientTest\HttpClientMiddleware;

use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManagerFactory;
use PHPUnit\Framework\TestCase;

class HttpClientMiddlewarePluginManagerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $object = new HttpClientMiddlewarePluginManagerFactory();
        $this->assertInstanceOf(HttpClientMiddlewarePluginManagerFactory::class, $object);
    }

}