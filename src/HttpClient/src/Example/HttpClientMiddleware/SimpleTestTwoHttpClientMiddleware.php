<?php

namespace HttpClient\Example\HttpClientMiddleware;

use GuzzleHttp\Promise\PromiseInterface;
use HttpClient\HttpClientMiddleware\AbstractHttpClientMiddleware;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\dic\InsideConstruct;

class SimpleTestTwoHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    /**
     * UserAgentHttpClientMiddleware constructor.
     *
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
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
        $logMessage = 'SimpleTestHttpClientMiddleware onRequest';
        $log = $request->getHeader('middleware_log') ?: [];
        $log[] = $logMessage;
        $this->logger->debug($logMessage);
        $request = $request->withHeader('middleware_log', $log);
        /**
         * @var PromiseInterface $promise
         */
        $promise = $this->nextHandlerProcess($request, $options);
        return $promise->then(function (ResponseInterface $response) {
            $logMessage = 'SimpleTestHttpClientMiddleware onFullfilled';
            $log = $this->request->getHeader('middleware_log') ?: [];
            $log[] = $logMessage;
            $this->logger->debug($logMessage);
            $response = $response->withHeader('middleware_log', $log);
            return $response;
        });
    }
}