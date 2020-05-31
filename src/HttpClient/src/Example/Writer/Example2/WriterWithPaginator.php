<?php


namespace HttpClient\Example\Writer\Example2;


use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use Jaeger\Tracer\Tracer;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\callback\PidKiller\WorkerProducer;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\dic\InsideConstruct;
use Throwable;

class WriterWithPaginator
{
    /**
     * @var DataStoreInterface
     */
    private $itemsDataStore;
    /**
     * @var WorkerProducer
     */
    private $workerProducer;

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
     * @param WorkerProducer       $workerProducer
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        DataStoresInterface $itemsDataStore,
        WorkerProducer $workerProducer,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->itemsDataStore = $itemsDataStore;
        $this->workerProducer = $workerProducer;
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
        $products = $data['products'];
        $nextPageUrl = $data['nextPageUrl'];
        $this->writeNextPageUrl($nextPageUrl);

        foreach ($products as $item) {
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

    private function writeNextPageUrl($nextPageUrl)
    {
        $payload = new ServerRequest('GET', $nextPageUrl);
        $this->workerProducer->__invoke($payload);
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