<?php


namespace HttpClientTest\Example\Writer\Example1;


use HttpClient\Example\Writer\Example2\UsageExample;
use HttpClient\Example\Writer\Example2\WriterWithPaginator;
use PHPUnit\Framework\TestCase;
use rollun\callback\PidKiller\WorkerProducer;
use rollun\callback\Queues\Adapter\FileAdapter;
use rollun\callback\Queues\QueueClient;
use rollun\datastore\DataStore\Memory;

class WriterWithPaginatorTest extends TestCase
{

    protected function createObject($dataStore, $workerProducer)
    {
        return new WriterWithPaginator($dataStore, $workerProducer);
    }

    public function testUsage()
    {
        $expectedResult = [
            ['id' => '00001', 'name' => 'Title1'],
            ['id' => '00002', 'name' => 'Title2'],
        ];
        $result = UsageExample::example1();
        $this->assertEquals($expectedResult, $result);
    }

    public function testCreate()
    {
        $queueAdapter = new FileAdapter(sprintf('%s/data/html', getcwd()));
        $queue = new QueueClient($queueAdapter, 'next_page_queue');
        $workerProducer = new WorkerProducer($queue);

        $writer = $this->createObject(new Memory(), $workerProducer);

        $this->assertInstanceOf(WriterWithPaginator::class, $writer);
    }
}