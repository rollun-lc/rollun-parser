<?php


namespace HttpClient\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use function GuzzleHttp\choose_handler;

class HttpClientAbstractFactory implements AbstractFactoryInterface
{

    public const KEY_CLASS = 'class';

    public const KEY_CONFIG = 'config';

    public const KEY_HANDLER = 'handler';

    public const KEY_MIDDLEWARE_LIST = 'middlewareList';

    public const BASE_CLASS = Client::class;

    /**
     * @var array
     */
    protected $selfConfig;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        if (isset($config[static::class][$requestedName])) {
            $this->selfConfig = $config[static::class][$requestedName];
            return true;
        }

        return false;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Client|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ClientInterface
    {
        $class = $this->selfConfig[static::KEY_CLASS] ?? static::BASE_CLASS;
        if (!is_a($class, ClientInterface::class, true)) {
            throw new InvalidArgumentException(sprintf("%s does not implement %s", $class, static::BASE_CLASS));
        }

        $handler = $this->selfConfig[static::KEY_HANDLER] ?? choose_handler();

        $middlewaresConfig = $this->selfConfig[static::KEY_MIDDLEWARE_LIST] ?? [];

        $clientConfig = $this->selfConfig[static::KEY_CONFIG] ?? [];

        $clientConfig['handler'] = $this->createHandlerStack($container, $handler, $middlewaresConfig);

        $httpClient = new $class($clientConfig);
        return $httpClient;
    }

    protected function createHandlerStack(ContainerInterface $container, $handler, $middlewaresConfig)
    {
        /**
         * @var HttpClientMiddlewarePluginManager $middlewarePluginManager
         */
        $middlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        $handlerStack = new HandlerStack($handler);

        foreach ($middlewaresConfig as $middlewareName => $middlewareConfig) {
            $middlewareFactoryFunction = is_string($middlewareConfig)
                ? $middlewarePluginManager->getHttpClientMiddlewareFactoryFunction($middlewareConfig)
                : call_user_func($middlewareConfig);
            $handlerStack->push($middlewareFactoryFunction, $middlewareName);
        }

        return $handlerStack;
    }
}
