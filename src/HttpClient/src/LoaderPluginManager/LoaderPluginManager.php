<?php


namespace HttpClient\LoaderPluginManager;

use Zend\Expressive\Container\Exception\InvalidServiceException;
use Zend\ServiceManager\AbstractPluginManager;

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
    public function validate($plugin)
    {
        if (!$plugin instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s expects only to create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))
            ));
        }
    }
}