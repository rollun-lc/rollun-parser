<?php


namespace HttpClient\Example\Loader\Example2;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Zend\Validator\ValidatorInterface;

class LoaderWithAuthentication
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var ClientInterface
     */
    protected $authenticationHttpClient;

    /**
     * @var ServerRequest
     */
    protected $authenticationRequest;

    /**
     * @var ValidatorInterface
     */
    protected $responseValidator;

    /**
     * @var CookieJarInterface
     */
    protected $cookieJar;

    /**
     * LoaderWithAuthentication constructor.
     *
     * @param ClientInterface    $httpClient
     * @param ClientInterface    $authenticationHttpClient
     * @param ServerRequest      $authenticationRequest
     * @param ValidatorInterface $responseValidator
     */
    public function __construct(
        ClientInterface $httpClient,
        ClientInterface $authenticationHttpClient,
        ServerRequest $authenticationRequest,
        ValidatorInterface $responseValidator
    ) {
        $this->httpClient = $httpClient;
        $this->authenticationHttpClient = $authenticationHttpClient;
        $this->authenticationRequest = $authenticationRequest;
        $this->responseValidator = $responseValidator;
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
        $this->authenticationHttpClient->send($this->authenticationRequest, $options);
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