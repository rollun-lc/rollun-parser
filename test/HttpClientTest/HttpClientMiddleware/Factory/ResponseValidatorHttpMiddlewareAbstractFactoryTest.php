<?php


namespace HttpClientTest\HttpClientMiddleware\Factory;


use HttpClient\HttpClientMiddleware\Factory\ResponseValidatorHttpMiddlewareAbstractFactory;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use PHPUnit\Framework\TestCase;

class ResponseValidatorHttpMiddlewareAbstractFactoryTest extends TestCase
{
    public function testCreate()
    {
        $object = new ResponseValidatorHttpMiddlewareAbstractFactory();
        $this->assertInstanceOf(ResponseValidatorHttpMiddlewareAbstractFactory::class, $object);
    }
}