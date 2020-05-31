<?php


namespace HttpClient\HttpClientMiddleware;


use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

interface HttpClientMiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface;

    /**
     * @param callable $nextHandler
     *
     * @return void
     */
    public function setHandler(callable $nextHandler): void;

    /**
     * @return callable|null
     */
    public function getHandler(): ?callable;
}