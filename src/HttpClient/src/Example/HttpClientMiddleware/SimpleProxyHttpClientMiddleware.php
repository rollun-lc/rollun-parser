<?php

namespace HttpClient\Example\HttpClientMiddleware;

use Closure;
use DateTime;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Promise\PromiseInterface;
use HttpClient\HttpClientMiddleware\AbstractHttpClientMiddleware;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonBotDetectionResponseValidator;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonCaptchaResponseValidator;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;
use rollun\dic\InsideConstruct;
use function GuzzleHttp\Promise\rejection_for;

class SimpleProxyHttpClientMiddleware extends AbstractHttpClientMiddleware
{

    public const DEFAULT_PROXY_ID = '0.0.0.0';
    /**
     * @var DataStoreInterface
     */
    protected $proxyDataStore;
    /**
     * @var array
     */
    protected $proxy;
    /**
     * @var DateTime
     */
    protected $startTime;

    /**
     * SimpleProxyHttpClientMiddleware constructor.
     *
     * @param DataStoreInterface   $proxyDataStore
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        DataStoreInterface $proxyDataStore,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        $this->proxyDataStore = $proxyDataStore;
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
     * @throws Exception
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $this->proxy = $this->getProxy();
        $options['proxy'] = $this->proxy['proxy'];
        $this->startTime = new DateTime();
        $this->logger->debug('Proxy has been set', ['proxy' => $this->proxy]);
        return $this->nextHandlerProcess($request, $options);
    }

    protected function onFullfilled(): ?Closure
    {
        return function (ResponseInterface $response) {
            $this->logger->debug(sprintf('%s - onFullfilled', __CLASS__));
            $endTime = new DateTime();
            $proxyRating = $this->createRating($this->startTime, $endTime);
            $this->logger->info('Proxy rating update', [
                'startTime' => $this->startTime->getTimestamp(),
                'endTime' => $endTime->getTimestamp(),
                'old_rating' => $this->proxy['rating'],
                'new_rating' => $proxyRating,
                'proxy' => $this->proxy
            ]);
            $this->updateProxy($this->proxy, $proxyRating);


            return $response;
        };
    }

    protected function onRejected(): ?Closure
    {
        return function ($reason) {
            $this->logger->debug(sprintf('%s - onRejected', __CLASS__));
            if ($reason instanceof Exception) {
                $validator = $this->getValidatorClassFromException($reason);
                if (in_array($validator, [
                    AmazonCaptchaResponseValidator::class,
                    AmazonBotDetectionResponseValidator::class,
                ])
                ) {
                    $endTime = new DateTime();
                    $proxyRating = -1;
                    $this->logger->info('Proxy rating update', [
                        'startTime' => $this->startTime->getTimestamp(),
                        'endTime' => $endTime->getTimestamp(),
                        'old_rating' => $this->proxy['rating'],
                        'new_rating' => $proxyRating,
                        'proxy' => $this->proxy
                    ]);
                    $this->updateProxy($this->proxy, $proxyRating);

                    /**
                     * If you need add additional info to $reason then throw new BadResponseException
                     */
                    throw new BadResponseException(
                        $reason->getMessage(),
                        $reason->getRequest(),
                        $reason->getResponse(),
                        $reason->getPrevious(),
                        array_merge($reason->getHandlerContext(), ['proxy_id' => 'proxyId'])
                    );
                }

            }
            return rejection_for($reason);
        };
    }

    /**
     * @return array|null
     */
    protected function getProxy(): ?array
    {
        return $this->proxyDataStore->read(static::DEFAULT_PROXY_ID);
    }

    /**
     * @param array $proxy
     * @param int   $rating
     */
    protected function updateProxy(array $proxy, int $rating)
    {
        $proxy['rating'] = $rating;
        $updateProxy = $this->proxyDataStore->update($proxy);
        $this->logger->info('Proxy updated', [
            'proxy' => $updateProxy
        ]);
    }

    /**
     * Create rating about proxy from 1 to 10
     *
     * @param DateTime $startTime
     * @param DateTime $endTime
     *
     * @return int
     */
    protected function createRating(
        DateTime $startTime,
        DateTime $endTime
    ): int {
        $delay = $endTime->getTimestamp() - $startTime->getTimestamp();
        $rating = call_user_func(function ($delay) {
            switch (true) {
                case $delay < 5:
                    return 6;
                case $delay < 7:
                    return 5;
                case $delay < 8:
                    return 4;
                case $delay < 12:
                    return 3;
                case $delay < 14:
                    return 2;
                default:
                    return 1;
            }
        }, $delay);
        $this->logger->debug('Proxy rating info', [
            'startTime' => $startTime->getTimestamp(),
            'endTime' => $endTime->getTimestamp(),
            'rating' => $rating,
        ]);
        return $rating;
    }

    public function __sleep()
    {
        return array_merge(parent::__sleep(), [
            'proxyDataStore'
        ]);
    }

}