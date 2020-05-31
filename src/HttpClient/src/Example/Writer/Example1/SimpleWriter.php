<?php


namespace HttpClient\Example\Writer\Example1;


use Exception;
use Jaeger\Tracer\Tracer;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\dic\InsideConstruct;
use Throwable;

class SimpleWriter
{
    /**
     * @var DataStoreInterface
     */
    private $itemsDataStore;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * SimpleWriter constructor.
     *
     * @param DataStoresInterface  $itemsDataStore
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        DataStoresInterface $itemsDataStore,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->itemsDataStore = $itemsDataStore;
        InsideConstruct::init([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class
        ]);
    }

    /**
     * @param $data
     *
     * @return mixed|void
     * @throws \rollun\utils\Json\Exception
     */
    public function write($data)
    {
        foreach ($data as $item) {
            try {
                /** @var string $dataStoreItemId */
                $dataStoreItemId = $this->itemsDataStore->getIdentifier();
                if ($this->itemsDataStore->has($item[$dataStoreItemId])) {
                    $this->itemsDataStore->update($item);
                    $this->logger->debug('Inventory item update.', [
                        'item' => $item
                    ]);
                } else {
                    $this->itemsDataStore->create($item);
                }
            } catch (Exception $e) {
                $this->logger->warning('ItemDetailsPageWriter. Could not write Product', [
                    'exception' => $e,
                    'product' => $item,
                ]);
            }
        }

    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        try {
            InsideConstruct::initWakeup([
                'logger' => LoggerInterface::class,
                'tracer' => Tracer::class,
            ]);
        } catch (Throwable $e) {
            throw new Exception("Can't deserialize itself. Reason: {$e->getMessage()}", 0, $e);
        }
    }

    public function __sleep()
    {
        return [
            'itemsDatastore',
        ];
    }
}