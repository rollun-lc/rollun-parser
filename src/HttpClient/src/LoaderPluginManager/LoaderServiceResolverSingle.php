<?php


namespace HttpClient\LoaderPluginManager;

use Exception;
use Laminas\ServiceManager\PluginManagerInterface;
use rollun\dic\InsideConstruct;

/**
 * Class LoaderServiceResolverSingle
 *
 * @package HttpClient\LoaderPluginManager
 */
class LoaderServiceResolverSingle
{
    /**
     * LoaderServiceResolverSingle constructor.
     *
     * @param PluginManagerInterface $loaderPluginManager
     * @param string                 $loaderServiceName
     */
    public function __construct(
        protected PluginManagerInterface $loaderPluginManager,
        protected string $loaderServiceName
    ) {
    }

    public function __invoke(): LoaderInterface
    {
        return $this->loaderPluginManager->get($this->loaderServiceName);
    }

    public function __sleep()
    {
        return ['loaderServiceName'];
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {

        try {
            InsideConstruct::initWakeup([
                'loaderPluginManager' => LoaderPluginManager::class,
            ]);
        } catch (Exception $e) {
            throw new Exception("Can't deserialize itself. Reason: {$e->getMessage()}", 0, $e);
        }
    }
}
