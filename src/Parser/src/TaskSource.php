<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\parser;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Log\LoggerInterface;
use rollun\callback\Callback\Interrupter\QueueFiller;
use rollun\callback\Promise\Interfaces\PayloadInterface;
use rollun\callback\Promise\SimplePayload;
use rollun\callback\Queues\QueueInterface;

class TaskSource extends QueueFiller
{
    const DEF_METHOD = 'GET';

    /**
     * Config for loader tasks
     * Example:
     *  [
     *      'uri' => 'site://example.com,
     *      'method' => 'GET'
     *      // ...
     *  ]
     * @var array
     */
    protected $config;

    /**
     * TaskSource constructor.
     * @param QueueInterface $queue
     * @param array $config
     * @param LoggerInterface|null $logger
     * @throws \ReflectionException
     */
    public function __construct(
        QueueInterface $queue,
        array $config,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($queue);
        $this->config = $config;
    }

    /**
     * @param mixed $value
     * @return PayloadInterface
     * @throws \rollun\utils\Json\Exception
     */
    public function __invoke($value): PayloadInterface
    {
        $result = [];

        foreach ($this->config as $taskConfig) {
            $method = strtoupper($taskConfig['method'] ?? 'GET');

            if (!isset($taskConfig['uri'])) {
                throw new \InvalidArgumentException("Option 'uri' is invalid");
            }

            $request = new ServerRequest($method, $taskConfig['uri']);
            $payload = parent::__invoke($request);
            $result[] = $payload->getPayload();
        }

        return new SimplePayload(null, $result);
    }

    public function __sleep()
    {
        $properties = parent::__sleep();

        return array_merge($properties, ['config']);
    }
}
