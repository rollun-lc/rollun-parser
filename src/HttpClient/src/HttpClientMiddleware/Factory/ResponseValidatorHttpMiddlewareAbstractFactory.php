<?php

namespace HttpClient\HttpClientMiddleware\Factory;

use HttpClient\HttpClientMiddleware\HttpClientMiddlewareInterface;
use HttpClient\HttpClientMiddleware\ResponseValidatorHttpClientMiddleware;
use HttpClient\HttpClientMiddleware\UserAgentHttpClientMiddleware;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use ReflectionException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * ...
 * ],
 * ResponseValidatorHttpMiddlewareAbstractFactory::class => [
 *      'serviseEmptyUserAgentMiddleware' => [
 *           'validators' => [
 *                  'AnotherValidatorServiceName', // validatorServiceName
 *           ],
 *      ],
 * ],
 *
 * Class ResponseValidatorHttpMiddlewareAbstractFactory
 *
 * @package HttpClient\HttpClientMiddleware\Factory
 */
class ResponseValidatorHttpMiddlewareAbstractFactory extends HttpClientMiddlewareAbstractFactory
    implements AbstractFactoryInterface
{
    public const KEY_VALIDATORS = 'validators';

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
        if (!isset($this->selfConfig[static::KEY_VALIDATORS])) {
            throw new InvalidArgumentException(sprintf("There is not %s", static::KEY_VALIDATORS));
        }

        $validators = [];
        $validatorsConfig = $this->selfConfig[static::KEY_VALIDATORS];
        foreach ($validatorsConfig as $validatorConfig) {
            $validators[] = $container->get($validatorConfig);
        }
        $middleware = new ResponseValidatorHttpClientMiddleware($validators);

        return $this->getMiddlewareWithHandler($middleware, $options);
    }
}