<?php


namespace HttpClient\HttpClientMiddleware;

use Interop\Container\ContainerInterface;
use rollun\callback\Middleware\CallablePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class HttpClientMiddlewarePluginManagerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return object|CallablePluginManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pluginConfig = $container->get('config')[static::class] ?: [];
        $pluginManager = new HttpClientMiddlewarePluginManager($container, $pluginConfig);
        return $pluginManager;
    }
}