<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\parser;


use Jaeger\Tag\StringTag;
use Jaeger\Tracer\Tracer;
use phpQuery;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use Zend\Validator\ValidatorInterface;

class AbstractParser
{
    public const HTML_DOCUMENTS_ERROR_PAGE_DIR = '/data/html/documents/errorPage/';
    /**
     * @var callable[]
     */
    private $strategies;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Tracer
     */
    private $tracer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var string
     */
    private $name;

    public function __construct(
        string $name,
        array $strategies,
        ValidatorInterface $validator,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->name = $name;
        $this->strategies = $strategies;
        $this->validator = $validator;
        InsideConstruct::init([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class,
        ]);
    }

    /**
     * @param string $htmData
     * @return mixed
     */
    public function __invoke(string $htmData)
    {
        $span = $this->tracer->start(sprintf('[%s]%s::__invoke', $this->name, self::class));
        $result = $this->parse($htmData);
        $this->tracer->finish($span);
        return $result;
    }

    /**
     * @param string $htmlData
     * @return mixed
     */
    public function parse(string $htmlData)
    {
        $span = $this->tracer->start(sprintf('[%s]%s::parse', $this->name, self::class));
        if ($htmlData === '') {
            throw new \RuntimeException('Html doc is empty.');
        }

        $document = PhpQuery::newDocument($htmlData);
        foreach ($this->strategies as $strategy) {
            $data = $strategy->parse($document);
            if ($this->validator->isValid($data)) {
                $this->tracer->finish($span);
                return $data;
            }
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->logger->debug('Parser {name} strategy {strategy} return not valid result. Reason: {messages}', [
                'name' => $this->name,
                'strategy' => get_class($strategy),
                'messages' => $this->validator->getMessages()
            ]);
        }
        $htmlDirPath = sprintf('%s%s%s', getcwd(), self::HTML_DOCUMENTS_ERROR_PAGE_DIR, $this->name);

        if (is_dir($htmlDirPath) && !mkdir($htmlDirPath) && !is_dir($htmlDirPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $htmlDirPath));
        }
        $htmlFilePath = sprintf('%s/%s.html', $htmlDirPath, uniqid(time() . '_', true));
        @file_put_contents($htmlFilePath, $htmlData);
        $span->addTag(new StringTag('htmlFilePath', $htmlFilePath));

        if (strpos($htmlData, '</body>') !== false) {
            throw new \RuntimeException("Not found strategy for valid parse this document. $htmlFilePath");
        }
        throw new \RuntimeException('Not found end of body.');
    }

    public function __sleep()
    {
        return ['strategies', 'validator', 'name'];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class,
        ]);
    }
}
