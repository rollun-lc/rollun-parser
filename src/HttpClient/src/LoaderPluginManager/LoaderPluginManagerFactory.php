<?php


namespace HttpClient\LoaderPluginManager;

use Interop\Container\ContainerInterface;
use rollun\callback\Middleware\CallablePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoaderPluginManagerFactory implements FactoryInterface
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
        $pluginManager = new LoaderPluginManager($container, $pluginConfig);
        return $pluginManager;
    }
}