<?php


namespace HttpClientTest\ServerRequest;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequestFactory;

class ServerRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new ServerRequestFactory();
        $this->assertInstanceOf(ServerRequestFactory::class, $factory);
    }

}