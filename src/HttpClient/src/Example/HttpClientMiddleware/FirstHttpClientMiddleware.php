<?php


namespace HttpClient\Example\HttpClientMiddleware;


use GuzzleHttp\Promise\PromiseInterface;
use HttpClient\HttpClientMiddleware\AbstractHttpClientMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FirstHttpClientMiddleware
 *
 * @package HttpClient\Example
 */
class FirstHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $responseLogMessage = 'FirstHttpClientMiddleware - request';
        $log = $request->getHeader('middleware_log') ?: [];
        $log[] = $responseLogMessage;
        $this->logger->debug($responseLogMessage);
        $request = $request->withHeader('middleware_log', $log);
        /**
         * @var PromiseInterface $promise
         */
        $promise = $this->nextHandlerProcess($request, $options);
        return $promise->then(
            function (ResponseInterface $response) use ($request) {
                $responseLogMessage = 'FirstHttpClientMiddleware - response';
                $log = $response->getHeader('middleware_log') ?: $this->request->getHeader('middleware_log');
                $log[] = $responseLogMessage;
                $this->logger->debug($responseLogMessage);
                return $response->withHeader('middleware_log', $log);
            }
        );
    }
}