<?php


namespace HttpClientTest\HttpClient;


use HttpClient\HttpClient\HttpClientAbstractFactory;
use PHPUnit\Framework\TestCase;

class HttpClientFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new HttpClientAbstractFactory();
        $this->assertInstanceOf(HttpClientAbstractFactory::class, $factory);
    }

}