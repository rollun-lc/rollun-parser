<?php


namespace rollun\parser;


use HttpClient\LoaderPluginManager\LoaderInterface;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;

class PageLoadParserCallback
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Tracer
     */
    private $tracer;

    public function __construct(
        LoaderInterface $loader,
        Parser $parser,
        LoggerInterface $logger,
        Tracer $tracer
    ) {
        $this->loader = $loader;
        $this->parser = $parser;
        $this->logger = $logger;
        $this->tracer = $tracer;
    }

    public function __sleep()
    {
        return [
            'loader', 'parser'
        ];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class,
        ]);
    }

    public function loadAndParse(RequestInterface $request)
    {
        $this->logger->debug('');
        $html = call_user_func($this->loader, $request);
        $this->logger->debug('ListPageParserCallback. Loader returns html', [
            'html_size' => is_string($html) ? strlen($html) : 0,
        ]);

        // run parser
        $parsedResult = $this->parser->parse($html);
        $this->logger->debug('ListPageParserCallback. Parser returns result', [
            'parsed_result' => is_array($parsedResult) ? [
                'products_count' => isset($parsedResult['products']) ? count($parsedResult['products']) : null,
            ] : [],
        ]);

        return $parsedResult;

    }

    public function __invoke(RequestInterface $request)
    {
        return $this->loadAndParse($request);
    }
}