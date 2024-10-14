<?php

namespace HttpClient\Example\Loader\Example3;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\Validator\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;

class AmazonLoader
{
    private CookieJarInterface $cookieJar;

    /**
     * AmazonLoader constructor.
     *
     * @param ClientInterface $httpClient
     * @param ClientInterface $zipCodeHttpClient
     * @param ServerRequest $zipCodeRequest
     * @param ValidatorInterface $responseValidator
     * @param DataStoreInterface $proxyDataStore
     * @param DataStoreInterface $proxyStatsDataStore
     */
    public function __construct(
        protected ClientInterface $httpClient,
        protected ClientInterface $zipCodeHttpClient,
        protected ServerRequest $zipCodeRequest,
        protected ValidatorInterface $responseValidator,
        protected DataStoreInterface $proxyDataStore,
        protected DataStoreInterface $proxyStatsDataStore
    ){}

    /**
     * @param ServerRequest $request
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(ServerRequest $request): string
    {
        $this->cookieJar = new CookieJar();

        $this->authenticate();
        $response = $this->loadPage($request);

        $html = $response->getBody()->__toString();
        return $html;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function authenticate(): void
    {
        $options = [
            'cookies' => $this->cookieJar,
        ];
        $this->zipCodeHttpClient->send($this->zipCodeRequest, $options);
    }

    /**
     * @param ServerRequest $request
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loadPage(ServerRequest $request): ResponseInterface
    {
        $options = [
            'cookies' => $this->cookieJar,
        ];
        return $this->httpClient->send($request, $options);
    }

}