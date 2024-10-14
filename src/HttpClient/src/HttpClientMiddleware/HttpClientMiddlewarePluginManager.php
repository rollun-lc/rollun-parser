<?php

namespace HttpClient\HttpClientMiddleware;

use Closure;
use HttpClient\HttpClientMiddleware\Factory\HttpClientMiddlewareAbstractFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

/**
 * Class HttpClientMiddlewarePluginManager
 *
 * @package HttpClient\Middleware
 */
class HttpClientMiddlewarePluginManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default; default to false (v2)
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Whether or not to share by default; default to false (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Default instance type
     *
     * @var string
     */
    protected $instanceOf = HttpClientMiddlewareInterface::class;

    /**
     * @param string $middlewareServiceName
     *
     * @return Closure
     */
    public function getHttpClientMiddlewareFactoryFunction(
        string $middlewareServiceName
    ): Closure {
        return function (callable $nextHandler) use ($middlewareServiceName) {
            $options = [
                HttpClientMiddlewareAbstractFactory::KEY_NEXT_HANDLER => $nextHandler
            ];

            return $this->get($middlewareServiceName, $options);
        };
    }

    /**
     * Validate plugin instance
     *
     * {@inheritDoc}
     */
    public function validate($instance): void
    {
        if (!$instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s expects only to create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }
}