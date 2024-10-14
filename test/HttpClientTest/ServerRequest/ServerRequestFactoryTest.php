<?php


namespace HttpClientTest\ServerRequest;

use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;

class ServerRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new ServerRequestFactory();
        $this->assertInstanceOf(ServerRequestFactory::class, $factory);
    }

}