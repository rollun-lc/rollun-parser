<?php

namespace HttpClient\HttpClientMiddleware\Factory;

use HttpClient\HttpClientMiddleware\HttpClientMiddlewareInterface;
use HttpClient\HttpClientMiddleware\UserAgentHttpClientMiddleware;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use ReflectionException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * ...
 * ],
 * InvokableHttpMiddlewareAbstractFactory::class => [
 *      'serviseSimpleHttpClientMiddleware' => SimpleHttpClientMiddleware::class,
 *      'serviseAnotherHttpClientMiddleware' => AnotherHttpClientMiddleware::class,
 * ],
 *
 * Class InvokableHttpMiddlewareAbstractFactory
 *
 * @package HttpClient\HttpClientMiddleware\Factory
 */
class InvokableHttpMiddlewareAbstractFactory extends HttpClientMiddlewareAbstractFactory
    implements AbstractFactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return UserAgentHttpClientMiddleware|object
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): HttpClientMiddlewareInterface {
        $middleware = new $this->selfConfig;
        return $this->getMiddlewareWithHandler($middleware, $options);
    }
}