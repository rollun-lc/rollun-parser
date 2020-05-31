<?php


namespace HttpClient\LoaderPluginManager;

use Exception;
use rollun\dic\InsideConstruct;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * Class LoaderServiceResolverSingle
 *
 * @package HttpClient\LoaderPluginManager
 */
class LoaderServiceResolverSingle
{
    /**
     * @var PluginManagerInterface
     */
    protected $loaderPluginManager;

    /**
     * @var string
     */
    protected $loaderServiceName;

    /**
     * LoaderServiceResolverSingle constructor.
     *
     * @param PluginManagerInterface $loaderPluginManager
     * @param string                 $loaderServiceName
     */
    public function __construct(
        PluginManagerInterface $loaderPluginManager,
        string $loaderServiceName
    ) {
        $this->loaderPluginManager = $loaderPluginManager;
        $this->loaderServiceName = $loaderServiceName;
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
