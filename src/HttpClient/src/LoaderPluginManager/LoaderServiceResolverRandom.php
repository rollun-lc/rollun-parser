<?php


namespace HttpClient\LoaderPluginManager;

use Exception;
use rollun\dic\InsideConstruct;
use Zend\ServiceManager\PluginManagerInterface;

class LoaderServiceResolverRandom
{
    /**
     * @var PluginManagerInterface
     */
    protected $loaderPluginManager;

    /**
     * @var array
     */
    protected $availableLoaderServiceList;

    public function __construct(
        PluginManagerInterface $loaderPluginManager,
        array $availableLoaderServiceList
    ) {
        $this->loaderPluginManager = $loaderPluginManager;
        $this->availableLoaderServiceList = $availableLoaderServiceList;
    }

    public function __invoke(): LoaderInterface
    {
        $currServiceIndex = rand(0, count($this->availableLoaderServiceList) - 1);
        $currServiceName = $this->availableLoaderServiceList[$currServiceIndex];
        return $this->loaderPluginManager->get($currServiceName);
    }

    public function __sleep()
    {
        return ['availableLoaderServiceList'];
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
