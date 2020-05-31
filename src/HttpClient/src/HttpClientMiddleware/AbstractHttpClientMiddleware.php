<?php


namespace HttpClient\HttpClientMiddleware;


use Closure;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use rollun\dic\InsideConstruct;
use Throwable;

/**
 * Class AbstractHttpClientMiddleware
 *
 * @package HttpClient\Middleware
 */
abstract class AbstractHttpClientMiddleware implements HttpClientMiddlewareInterface
{

    public const KEY_VALIDATOR = 'validator';

    /** @var callable */
    protected $nextHandler;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Tracer
     */
    protected $tracer;

    /**
     * AbstractHttpClientMiddleware constructor.
     *
     * @param LoggerInterface|null $logger
     * @param Tracer|null          $tracer
     *
     * @throws ReflectionException
     */
    public function __construct(
        LoggerInterface $logger = null,
        Tracer $tracer = null
    ) {
        InsideConstruct::init([
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class
        ]);
    }

    abstract public function __invoke(RequestInterface $request, array $options): PromiseInterface;

    /**
     * @param callable $nextHandler
     */
    public function setHandler(callable $nextHandler): void
    {
        $this->nextHandler = $nextHandler;
    }

    public function getHandler(): ?callable
    {
        return $this->nextHandler;
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    protected function nextHandlerProcess(RequestInterface $request, array $options): PromiseInterface
    {
        $promise = $this->nextHandler->__invoke($request, $options);
        $this->request = $request;
        return $this->getPromiseWithThen($promise);
    }

    /**
     * @return Closure
     * @deprecated
     */
    public static function getHttpClientMiddlewareFactoryFunction()
    {
        return function (callable $nextHandler) {
            $middleware = new static();
            $middleware->setHandler($nextHandler);
            return $middleware;
        };
    }

    /**
     * @param PromiseInterface $promise
     *
     * @return PromiseInterface
     */
    protected function getPromiseWithThen(PromiseInterface $promise): PromiseInterface
    {
        $onFullfilled = static::onFullfilled();
        $onRejected = static::onRejected();
        if ($onFullfilled || $onRejected) {
            $promise = $promise->then($onFullfilled, $onRejected);
        }
        return $promise;
    }

    /**
     * @param Exception $exception
     *
     * @return string|null
     */
    protected function getValidatorClassFromException(Exception $exception)
    {
        if ($exception instanceof RequestException) {
            $handlerContext = $exception->getHandlerContext();
            return isset($handlerContext[static::KEY_VALIDATOR]) ? $handlerContext[static::KEY_VALIDATOR] : null;
        }
        return null;
    }

    /**
     * @return Closure
     */
    protected function onFullfilled(): ?Closure
    {
        return null;
    }

    /**
     * @return Closure
     */
    protected function onRejected(): ?Closure
    {
        return null;
    }


    public function __sleep()
    {
        return [
            'nextHandler',
            'request',
        ];
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        try {
            InsideConstruct::initWakeup(
                [
                    'logger' => LoggerInterface::class,
                    'tracer' => Tracer::class
                ]
            );
        } catch (Throwable $e) {
            throw new Exception("Can't deserialize itself. Reason: {$e->getMessage()}", 0, $e);
        }
    }
}