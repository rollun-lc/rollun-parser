<?php

namespace HttpClient\HttpClientMiddleware;

use Closure;
use HttpClient\HttpClientMiddleware\Factory\HttpClientMiddlewareAbstractFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

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
    public function validate($plugin)
    {
        if (!$plugin instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s expects only to create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))
            ));
        }
    }
}