<?php

namespace HttpClient\HandlerStack;

use GuzzleHttp\HandlerStack;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use function GuzzleHttp\choose_handler;

/**
 * ...
 * ],
 * HandlerStackAbstractFactory::class => [
 *      'HandlerStackEmptyServiceName' => [], // curl middleware only
 *      'HandlerStackStandartServiceName' => [
 *           'http_errors' => [GuzzleHttp\Middleware::class, 'httpErrors'],
 *           'allow_redirects' => [GuzzleHttp\Middleware::class, 'redirect'],
 *           'cookies' => [GuzzleHttp\Middleware::class, 'cookies'],
 *           'prepare_body' => [GuzzleHttp\Middleware::class, 'prepareBody'],
 *      ],
 *      'HandlerStackCustomServiceName' => [
 *           'http_errors' => [GuzzleHttp\Middleware::class, 'httpErrors'],
 *           'prepare_body' => [GuzzleHttp\Middleware::class, 'prepareBody'],
 *           'my_own_middleware' => MyOwnMiddleware::class, //serviceName
 *      ],
 * ],
 *
 * Class HandlerStackAbstractFactory
 *
 * @package HttpClient\HandlerStack
 */
class HandlerStackAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $selfConfig;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return HandlerStack
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var HttpClientMiddlewarePluginManager $middlewarePluginManager
         */
        $middlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        $handlerStack = new HandlerStack(choose_handler());

        foreach ($this->selfConfig as $middlewareName => $middlewareConfig) {
            $middlewareFactoryFunction = is_string($middlewareConfig)
                ? $middlewarePluginManager->getHttpClientMiddlewareFactoryFunction($middlewareConfig)
                : call_user_func($middlewareConfig);
            $handlerStack->push($middlewareFactoryFunction, $middlewareName);
        }

        return $handlerStack;
    }

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
}