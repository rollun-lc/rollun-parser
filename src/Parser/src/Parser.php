<?php


namespace rollun\parser;


use Jaeger\Tag\StringTag;
use Jaeger\Tracer\Tracer;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use Zend\Validator\ValidatorInterface;

class Parser implements StrategyInterface
{
    use CallableStrategyTrait;

    public const HTML_DOCUMENTS_ERROR_PAGE_DIR = '/data/html/documents/errorPage/';


    /**
     * @var string
     */
    private $name;

    /**
     * @var StrategyInterface[]
     */
    private $strategies;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * Parser constructor.
     * @param string $name
     * @param array $strategies
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param Tracer $tracer
     */
    public function __construct(
        string $name,
        array $strategies,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        Tracer $tracer
    ) {
        $this->name = $name;
        $this->strategies = $strategies;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->tracer = $tracer;
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

    protected function saveInvalidPage(string $html)
    {
        $htmlDirPath = sprintf('%s%s%s', getcwd(), self::HTML_DOCUMENTS_ERROR_PAGE_DIR, $this->name);

        if (!is_dir($htmlDirPath)) {
            mkdir($htmlDirPath, 0700, true);
        }

        if (!is_dir($htmlDirPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $htmlDirPath));
        }
        $htmlFilePath = sprintf('%s/%s.html', $htmlDirPath, uniqid(time() . '_', true));
        $htmlFileData = $html;
        @file_put_contents($htmlFilePath, $htmlFileData . "\n");

        $this->logger->critical('Not found strategy for valid parse this document.', [
            'htmlFilePath' => $htmlFilePath,
        ]);
        return $htmlFilePath;
    }

    public function parse(string $html)
    {
        $span = $this->tracer->start(sprintf('[%s]%s::parse', $this->name, self::class));
        if (strlen($html) <= 10) {
            throw new \RuntimeException('Html doc is empty.');
        }

        foreach ($this->strategies as $strategy) {
            $data = $strategy->parse($html);
            if ($this->validator->isValid($data)) {
                $this->tracer->finish($span);
                return $data;
            }
            $this->logger->debug(
                'Parser {name} strategy {strategy} return not valid result. Reason: {messages}',
                [
                    'name' => $this->name,
                    'strategy' => get_class($strategy),
                    'messages' => $this->validator->getMessages(),
                ]
            );
        }

        if (strpos($html, '</body>') !== false) {
            $htmlFilePath = $this->saveInvalidPage($html);
            throw new \RuntimeException("Not found strategy for valid parse this document. $htmlFilePath");
        }
        throw new \RuntimeException('Not found end of body.');
    }
}