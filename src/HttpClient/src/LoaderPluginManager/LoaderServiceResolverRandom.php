<?php


namespace HttpClient\LoaderPluginManager;

use Exception;
use Laminas\ServiceManager\PluginManagerInterface;
use rollun\dic\InsideConstruct;

class LoaderServiceResolverRandom
{
    public function __construct(
        protected PluginManagerInterface $loaderPluginManager,
        protected array $availableLoaderServiceList
    ) {
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
