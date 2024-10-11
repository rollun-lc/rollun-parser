<?php


namespace HttpClient\Example\Writer\Example2;

use rollun\callback\PidKiller\WorkerProducer;
use rollun\callback\Queues\Adapter\FileAdapter;
use rollun\callback\Queues\QueueClient;
use rollun\datastore\DataStore\Memory;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode;
use Xiag\Rql\Parser\Query;

class UsageExample
{
    public static function example1()
    {
        // create dataStore
        $dataStore = new Memory(['id', 'name']);

        $queueAdapter = new FileAdapter(sprintf('%s/data/html', getcwd()));
        $queue = new QueueClient($queueAdapter, 'next_page_queue');
        $workerProducer = new WorkerProducer($queue);

        // create Writer
        $writer = new WriterWithPaginator(
            $dataStore,
            $workerProducer
        );

        // usage
        $data = [
            'products' => [
                ['id' => '00001', 'name' => 'Title1'],
                ['id' => '00002', 'name' => 'Title2'],
            ],
            'nextPageUrl' => 'https://www.google.com/search?q=test+page&start=10',
        ];
        $writer->write($data);

        //read data from dataStore
        $query = new Query();
        $query->setQuery(
            new LikeNode('name', '%it%')
        );
        $writtenData = $dataStore->query($query);
        $queue->purgeQueue();
        return $writtenData;
    }
}