<?php


namespace HttpClient\Example\HttpClientMiddleware;


use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use HttpClient\HttpClientMiddleware\AbstractHttpClientMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $request = $request->withHeader('request_header', $this->getHeaderValue());
        /**
         * @var Promise $promise
         */
        $nextHandler = $this->nextHandler;
        $promise = $nextHandler($request, $options);
        return $promise->then(
            function (ResponseInterface $response) use ($request) {
                return $response->withHeader('response_header', $request->getHeader('request_header'));
            }
        );
    }

    protected function getHeaderValue()
    {
        return 'testValue';
    }

}