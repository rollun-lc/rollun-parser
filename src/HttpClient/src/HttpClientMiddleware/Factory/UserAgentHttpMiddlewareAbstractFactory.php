<?php

namespace HttpClient\HttpClientMiddleware\Factory;

use HttpClient\HttpClientMiddleware\HttpClientMiddlewareInterface;
use HttpClient\HttpClientMiddleware\UserAgentHttpClientMiddleware;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use ReflectionException;

/**
 * ...
 * ],
 * UserAgentHttpMiddlewareAbstractFactory::class => [
 *      'serviseEmptyUserAgentMiddleware' => [
 *           'userAgent' => '',
 *      ],
 *      'serviseCustomUserAgentMiddleware' => [
 *           'userAgent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0',
 *      ],
 * ],
 *
 * Class UserAgentHttpMiddlewareAbstractFactory
 *
 * @package HttpClient\HttpClientMiddleware\Factory
 */
class UserAgentHttpMiddlewareAbstractFactory extends HttpClientMiddlewareAbstractFactory
    implements AbstractFactoryInterface
{
    public const KEY_USER_AGENT = 'userAgent';

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return UserAgentHttpClientMiddleware|object
     * @throws ReflectionException
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): HttpClientMiddlewareInterface {
        if (!isset($this->selfConfig[static::KEY_USER_AGENT])) {
            throw new InvalidArgumentException(sprintf("There is not %s", static::KEY_USER_AGENT));
        }

        $userAgent = $this->selfConfig[static::KEY_USER_AGENT];
        $middleware = new UserAgentHttpClientMiddleware(
            $userAgent
        );

        return $this->getMiddlewareWithHandler($middleware, $options);
    }
}