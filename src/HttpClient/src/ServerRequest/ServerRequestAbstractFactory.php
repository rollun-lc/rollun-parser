<?php


namespace HttpClient\ServerRequest;

use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ServerRequestAbstractFactory implements AbstractFactoryInterface
{

    public const KEY_METHOD = 'method';

    public const KEY_URI = 'uri';

    public const KEY_HEADERS = 'headers';

    public const KEY_BODY = 'body';

    public const KEY_VERSION = 'version';

    public const KEY_SERVER_PARAMS = 'serverParams';

    public const BASE_METHOD = 'GET';

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
     * @return ServerRequest
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ServerRequest
    {
        $method  = $this->selfConfig[static::KEY_METHOD] ?? static::BASE_METHOD;

        if (!isset($this->selfConfig[static::KEY_URI])) {
            throw new InvalidArgumentException(sprintf("Required config param %s is missed", static::KEY_URI));
        }

        $uri = $this->selfConfig[static::KEY_URI];
        $headers = $this->selfConfig[static::KEY_HEADERS] ?? [];
        $body = $this->selfConfig[static::KEY_BODY] ?? null;
        $version = $this->selfConfig[static::KEY_VERSION] ?? '1.1';
        $serverParams = $this->selfConfig[static::KEY_SERVER_PARAMS] ?? [];

        return new ServerRequest($method, $uri, $headers, $body, $version, $serverParams);
    }
}
