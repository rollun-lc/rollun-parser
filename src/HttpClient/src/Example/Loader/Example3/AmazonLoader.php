<?php


namespace HttpClient\Example\Loader\Example3;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;
use Zend\Validator\ValidatorInterface;

class AmazonLoader
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var ClientInterface
     */
    protected $zipCodeHttpClient;

    /**
     * @var ServerRequest
     */
    protected $zipCodeRequest;

    /**
     * @var ValidatorInterface
     */
    protected $responseValidator;

    /**
     * @var DataStoreInterface
     */
    protected $proxyDataStore;

    /**
     * @var DataStoreInterface
     */
    protected $proxyStatsDataStore;

    /**
     * AmazonLoader constructor.
     *
     * @param ClientInterface    $httpClient
     * @param ClientInterface    $zipCodeHttpClient
     * @param ServerRequest      $zipCodeRequest
     * @param ValidatorInterface $responseValidator
     * @param DataStoreInterface $proxyDataStore
     * @param DataStoreInterface $proxyStatsDataStore
     */
    public function __construct(
        ClientInterface $httpClient,
        ClientInterface $zipCodeHttpClient,
        ServerRequest $zipCodeRequest,
        ValidatorInterface $responseValidator,
        DataStoreInterface $proxyDataStore,
        DataStoreInterface $proxyStatsDataStore
    ) {
        $this->httpClient = $httpClient;
        $this->zipCodeHttpClient = $zipCodeHttpClient;
        $this->zipCodeRequest = $zipCodeRequest;
        $this->responseValidator = $responseValidator;
        $this->proxyDataStore = $proxyDataStore;
        $this->proxyStatsDataStore = $proxyStatsDataStore;
    }

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
    protected function authenticate()
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