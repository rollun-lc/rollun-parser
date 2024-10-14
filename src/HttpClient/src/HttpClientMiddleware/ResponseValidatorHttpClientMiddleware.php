<?php


namespace HttpClient\HttpClientMiddleware;

use Closure;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Jaeger\Tracer\Tracer;
use Laminas\Validator\ValidatorInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\dic\InsideConstruct;
use Throwable;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\rejection_for;

class ResponseValidatorHttpClientMiddleware extends AbstractHttpClientMiddleware
{
    /**
     * ResponseValidatorHttpClientMiddleware constructor.
     *
     * @param ValidatorInterface[] $responseValidators
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        protected array $responseValidators,
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
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
        return $this->nextHandlerProcess($request, $options);
    }

    /**
     * @return Closure|null
     */
    protected function onFullfilled(): ?Closure
    {
        return function (ResponseInterface $response) {
            $this->logger->debug(sprintf('%s - onFullfilled', __CLASS__));
            return $this->validateResponse($response) ?: $response;
        };
    }

    /**
     * @return Closure|null
     */
    protected function onRejected(): ?Closure
    {
        return function ($reason) {
            $this->logger->debug(
                sprintf('%s - onRejected', __CLASS__),
                [
                    'exception' => $reason,
                ]
            );

            if ($reason instanceof RequestException) {
                $response = $reason->getResponse();
                if ($response instanceof ResponseInterface) {
                    return $this->validateResponse($response) ?: promise_for($response);
                }
            }
            return rejection_for($reason);
        };
    }

    /**
     * @param ResponseInterface $response
     *
     * @return PromiseInterface|null
     * @throws Throwable
     */
    protected function validateResponse(ResponseInterface $response): ?PromiseInterface
    {
        foreach ($this->responseValidators as $validator) {
            if (!$validator->isValid($response)) {
                $messages = $validator->getMessages();
                if (!is_array($messages)) {
                    $messages = [$messages];
                }
                foreach ($messages as $message) {
                    $this->logger->debug(
                        'ResponseValidatorHttpClientMiddleware::validateResponse - BadResponseException',
                        [
                            'message' => $message,
                            'validator' => get_class($validator),
                        ]
                    );
                    throw new BadResponseException($message, $this->request, $response, null, [
                        static::KEY_VALIDATOR => get_class($validator)
                    ]);
                }
            }
        }
        return null;
    }
}
