<?php


namespace HttpClient\HttpClientMiddleware;

use Closure;
use Exception;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Promise\PromiseInterface;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonZipCodeResponseValidator;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\dic\InsideConstruct;
use function GuzzleHttp\Promise\rejection_for;

class CookieHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    /**
     * @var CookieJarInterface
     */
    protected $cookieJar;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * UserAgentHttpClientMiddleware constructor.
     *
     * @param CookieJarInterface|null $cookieJar
     * @param LoggerInterface|null    $logger
     * @param Tracer|null             $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        CookieJarInterface $cookieJar,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->cookieJar = $cookieJar;
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
        $request = $this->cookieJar->withCookieHeader($request);
        $this->request = $request;
        $this->logger->debug('Cookies have been set', ['cookies' => $this->cookieJar->toArray()]);
        return $this->nextHandlerProcess($request, $options);
    }

    /**
     * @return Closure|null
     */
    public function onFullfilled(): ?Closure
    {
        return function (ResponseInterface $response) {

            $this->logger->debug(sprintf('%s - onFullfilled', __CLASS__));
            $this->cookieJar->extractCookies($this->request, $response);
            $this->logger->debug('Cookies have been extracted', ['cookies' => $this->cookieJar->toArray()]);
            return $response;
        };
    }

    /**
     * @return Closure|null
     */
    public function onRejected(): ?Closure
    {
        return function ($reason) {

            $this->logger->debug(sprintf('%s - onRejected', __CLASS__));

            if ($reason instanceof Exception) {
                $validator = $this->getValidatorClassFromException($reason);
                if (AmazonZipCodeResponseValidator::class === $validator) {
                    /**
                     * Cookies failed. Clear them
                     */
                    $this->cookieJar->clear();
                    $this->logger->debug('CookieHttpClientMiddleware: CookieJar has been cleared');
                    /**
                     * @todo throw HttpClientMiddlewareException( with options ['cookie_id' => $this->cookieId]
                     */
                    throw new BadResponseException(
                        $reason->getMessage(),
                        $reason->getRequest(),
                        $reason->getResponse(),
                        $reason->getPrevious(),
                        array_merge($reason->getHandlerContext(), ['cookie_id' => 'testCookieId'])
                    );
                }

            }
            return rejection_for($reason);
        };
    }
}