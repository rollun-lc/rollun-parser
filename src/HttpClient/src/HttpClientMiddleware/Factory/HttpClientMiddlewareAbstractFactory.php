<?php

namespace HttpClient\HttpClientMiddleware\Factory;

use HttpClient\HttpClientMiddleware\HttpClientMiddlewareInterface;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class HttpClientMiddlewareAbstractFactory
 *
 * @package HttpClient\HttpClientMiddleware\Factory
 */
abstract class HttpClientMiddlewareAbstractFactory implements AbstractFactoryInterface
{
    public const KEY_NEXT_HANDLER = 'nextHandler';

    /**
     * @var array
     */
    protected $selfConfig;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        if (isset($config[static::class][$requestedName])) {
            $this->selfConfig = $config[static::class][$requestedName];
            return true;
        }

        return false;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return HttpClientMiddlewareInterface
     */
    abstract public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): HttpClientMiddlewareInterface;


    /**
     * @param array|null $options
     *
     * @return callable
     */
    protected function getNextHandler(array $options = null): callable
    {
        if (!isset($options[static::KEY_NEXT_HANDLER])) {
            throw new InvalidArgumentException(sprintf("There is not %s", static::KEY_NEXT_HANDLER));
        }

        $nextHandler = $options[static::KEY_NEXT_HANDLER];
        if (!is_callable($nextHandler)) {
            throw new InvalidArgumentException(sprintf(
                "%s must be callable, %s given.",
                static::KEY_NEXT_HANDLER,
                is_object($nextHandler) ? get_class($nextHandler) : gettype($nextHandler)
            ));
        }

        return $nextHandler;
    }

    /**
     * @param HttpClientMiddlewareInterface $middleware
     * @param array|null                    $options
     *
     * @return HttpClientMiddlewareInterface
     */
    protected function getMiddlewareWithHandler(
        HttpClientMiddlewareInterface $middleware,
        array $options = null
    ): HttpClientMiddlewareInterface {
        $nextHandler = $this->getNextHandler($options);
        $middleware->setHandler($nextHandler);
        return $middleware;
    }

}