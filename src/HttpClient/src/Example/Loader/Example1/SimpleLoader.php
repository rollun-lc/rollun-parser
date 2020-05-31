<?php


namespace HttpClient\Example\Loader\Example1;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleLoader
{

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * SimpleLoader constructor.
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(
        ClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     * @throws GuzzleException
     */
    public function __invoke(RequestInterface $request): string
    {
        $response = $this->httpClient->send($request);
        $html = $response->getBody()->__toString();
        return $html;
    }
}