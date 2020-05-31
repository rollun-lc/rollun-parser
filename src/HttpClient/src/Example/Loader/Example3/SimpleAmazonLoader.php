<?php


namespace HttpClient\Example\Loader\Example3;

use GuzzleHttp\ClientInterface;
use HttpClient\LoaderPluginManager\LoaderInterface;
use Psr\Http\Message\RequestInterface;

class SimpleAmazonLoader implements LoaderInterface
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(RequestInterface $request): string
    {
        $response = $this->httpClient->send($request);
        $html = $response->getBody()->__toString();
        return $html;
    }
}
