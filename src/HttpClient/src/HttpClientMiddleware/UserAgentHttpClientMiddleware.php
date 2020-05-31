<?php


namespace HttpClient\HttpClientMiddleware;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\dic\InsideConstruct;

class UserAgentHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    /**
     * @var string
     */
    protected $userAgent;

    /**
     * UserAgentHttpClientMiddleware constructor.
     *
     * @param string|null          $userAgent
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        string $userAgent = null,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->userAgent = $userAgent;
        InsideConstruct::setConstructParams([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class
        ]);
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $request = $request->withHeader('User-Agent', $this->userAgent);
        $this->logger->debug('UserAgent have been set', ['user_agent' => $this->userAgent]);
        return $this->nextHandlerProcess($request, $options);
    }
}