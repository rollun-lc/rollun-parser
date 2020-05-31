<?php


namespace HttpClentTest;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use HttpClient\HandlerStack\HandlerStackAbstractFactory;
use PHPUnit\Framework\TestCase;

class HandlerStackFactoryTest extends TestCase
{
    public function testCreate()
    {
        $object = new HandlerStackAbstractFactory();
        $this->assertInstanceOf(HandlerStackAbstractFactory::class, $object);
    }

    public function testCreateEmptyHandlerStack()
    {
        global $container;
        /**
         * @var HandlerStack $emptyHandlerStack
         */
        $emptyHandlerStack = $container->get('HandlerStackEmptyServiceName');
        $this->assertInstanceOf(HandlerStack::class, $emptyHandlerStack);
        $this->assertTrue($emptyHandlerStack->hasHandler());
        $this->assertIsCallable($emptyHandlerStack->resolve());
    }

    public function testCreateTestHandlerStack()
    {
        global $container;
        /**
         * @var HandlerStack $testHandlerStack
         */
        $testHandlerStack = $container->get('HandlerStackEmptyServiceName');
        $mockHandler = new MockHandler([
            new Response(200),
            new Response(301),
        ]);
        $testHandlerStack->setHandler($mockHandler);
        $this->assertInstanceOf(HandlerStack::class, $testHandlerStack);
        $this->assertTrue($testHandlerStack->hasHandler());
        $this->assertIsCallable($testHandlerStack->resolve());
    }

    public function testCreateStandartHandler()
    {
        global $container;
        /**
         * @var HandlerStack $standartHandler
         */
        $standartHandler = $container->get('HandlerStackStandartServiceName');
        $this->assertInstanceOf(HandlerStack::class, $standartHandler);
        $this->assertTrue($standartHandler->hasHandler());
        $this->assertIsCallable($standartHandler->resolve());
    }
}