<?php


namespace HttpClientTest\HttpClientMiddleware;

use Closure;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use HttpClient\HttpClientMiddleware\Factory\HttpClientMiddlewareAbstractFactory;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use PHPUnit\Framework\TestCase;

class HttpClientMiddlewarePluginManagerTest extends TestCase
{
    public function testCreate()
    {
        global $container;
        $object = $container->get(HttpClientMiddlewarePluginManager::class);
        $this->assertInstanceOf(HttpClientMiddlewarePluginManager::class, $object);
    }

    public function testGetMiddlewareFactoryFunction()
    {
        global $container;
        $object = $container->get(HttpClientMiddlewarePluginManager::class);
        $factoryFunction = $object->getHttpClientMiddlewareFactoryFunction('SimpleUserAgentHttpClientMiddleware');
        $this->assertIsCallable($factoryFunction);
    }

    public function testCreateNotSameObjects()
    {
        global $container;
        $options = [
            HttpClientMiddlewareAbstractFactory::KEY_NEXT_HANDLER => new MockHandler([new Response(200)])
        ];
        /**
         * @var HttpClientMiddlewarePluginManager $middlewarePluginManager
         */
        $middlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        $middlewarePluginManager2 = $container->get(HttpClientMiddlewarePluginManager::class);

        $this->assertSame($middlewarePluginManager, $middlewarePluginManager2);

        $middleware1 = $middlewarePluginManager->get('SimpleUserAgentHttpClientMiddleware', $options);
        $middleware2 = $middlewarePluginManager->get('SimpleUserAgentHttpClientMiddleware', $options);
        $this->assertNotSame($middleware1, $middleware2);

    }

    public function testGetFactoryFunction()
    {
        global $container;

        /**
         * @var HttpClientMiddlewarePluginManager $middlewarePluginManager
         */
        $middlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        $factoryFunction = $middlewarePluginManager->getHttpClientMiddlewareFactoryFunction('SimpleUserAgentHttpClientMiddleware');
        $this->assertInstanceOf(Closure::class, $factoryFunction);
    }
}