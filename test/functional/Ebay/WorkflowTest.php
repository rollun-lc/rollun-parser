<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace test\functional\Ebay;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use rollun\callback\PidKiller\Worker;
use rollun\callback\Queues\Adapter\FileAdapter;
use rollun\callback\Queues\QueueClient;
use rollun\datastore\DataStore\HttpClient;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\parser\ResponseValidator\StatusOk;
use test\functional\Ebay\Assets\RollunLoader;
use test\functional\Ebay\Assets\RollunParser;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Http\Client;

class WorkflowTest extends TestCase
{
    protected function setUp()
    {
        if (!file_exists('/tmp/documents')) {
            mkdir('/tmp/documents');
        }
    }

    protected function tearDown()
    {
        $this->rrmdir('/tmp/documents');
        $this->rrmdir('/tmp/test');
    }

    public function testWorkflowWithoutWorkers()
    {
        $uri = 'https://rollun.com/';
        $proxyManagerUri = getenv('PROXY_MANAGER_URI');
        $proxyDataStore = new HttpClient(new Client(), $proxyManagerUri);

        $documentQueue = new QueueClient(new FileAdapter('/tmp/test'), 'test');
        $validator = new StatusOk();
        $loader = new RollunLoader($proxyDataStore, $documentQueue, [], $validator);

        do {
            $continue = false;
            $request = (new ServerRequestFactory())->createServerRequest('GET', $uri);

            try {
                $loader($request);
            } catch (\Throwable $e) {
                $continue = true;
            }
        } while ($continue);

        /** @var DataStoresInterface|MockObject $parseResultDsMock */
        $parseResultDsMock = $this->getMockBuilder(DataStoresInterface::class)->getMock();
        $parseResultDsMock->expects($this->once())->method('create')->with(['About Us']);
        $parser = new RollunParser($parseResultDsMock);

        $worker = new Worker($documentQueue, $parser, null);
        $worker();
        $this->assertTrue(true);
    }

    public function testSerializeLoader()
    {
        $proxyManagerUri = getenv('PROXY_MANAGER_URI');
        $proxyDataStore = new HttpClient(new Client(), $proxyManagerUri);

        $documentQueue = new QueueClient(new FileAdapter('/tmp/test'), 'test');
        $validator = new StatusOk();
        $loader = new RollunLoader($proxyDataStore, $documentQueue, [], $validator);

        $this->assertTrue(boolval(unserialize(serialize($loader))));
    }

    public function testSerializeParser()
    {
        /** @var DataStoresInterface|MockObject $parseResultDsMock */
        $parseResultDsMock = $this->getMockBuilder(DataStoresInterface::class)->getMock();
        $parser = new RollunParser($parseResultDsMock);

        $this->assertTrue(boolval(unserialize(serialize($parser))));
    }

    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
