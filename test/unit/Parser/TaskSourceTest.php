<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace test\unit\Parser;

use PHPUnit\Framework\TestCase;
use rollun\callback\Queues\Adapter\FileAdapter;
use rollun\callback\Queues\QueueClient;
use rollun\parser\TaskSource;

class TaskSourceTest extends TestCase
{
    public function testSerialize()
    {
        $queue = new QueueClient(new FileAdapter('/tmp/test'), 'test');
        $object = new TaskSource($queue, [
            'uri' => 'example',
        ]);
        $this->assertTrue(boolval(unserialize(serialize($object))));
    }
}
