<?php


namespace HttpClient\LoaderPluginManager;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

class LoaderPluginManager extends AbstractPluginManager
{

    protected $abstractFactories = [];
    /**
     * Default instance type
     *
     * @var string
     */
    protected $instanceOf = LoaderInterface::class;

    /**
     * Validate plugin instance
     *
     * {@inheritDoc}
     */
    public function validate($instance): void
    {
        if (!$instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s expects only to create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }
}