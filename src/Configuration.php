<?php

namespace Improv;

use Improv\Configuration\Exceptions\InvalidKeyException;
use Improv\Configuration\Mapper;
use Improv\Configuration\MapperFactory;

class Configuration
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var MapperFactory
     */
    private $factory_mapper;

    /**
     * @param array              $config
     * @param string             $prefix
     * @param MapperFactory|null $mapper_factory
     */
    public function __construct(array $config, $prefix = '', MapperFactory $mapper_factory = null)
    {

        $this->config         = $config;
        $this->factory_mapper = ($mapper_factory) ?: new MapperFactory();

        if (!$prefix) {
            return;
        }

        $config = array_filter($config, function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);

        $keys = array_map(
            function ($key) use ($prefix) {
                return str_replace($prefix, '', $key);
            },
            array_keys($config)
        );

        $this->config = array_combine($keys, array_values($config));

    }

    /**
     * Check if a given key exists in the Configuration object.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->config[ $key ]);
    }

    /**
     * Configures a Mapper object for the given Key
     *
     * The Mapper object can be configured
     *
     * @param $key
     * @return Mapper
     *
     * @throws InvalidKeyException
     */
    public function map($key)
    {
        $this->checkKeyExists($key);

        $mapper = $this->factory_mapper->createNew($this->config[$key]);

        $this->config[$key] = $mapper;

        return $mapper;
    }

    /**
     * Obtain the value for a given key.
     *
     * If a mapper is used, returns the mapped value by invoking the mapper
     *
     * @param string $key
     * @return mixed
     *
     * @throws InvalidKeyException
     */
    public function get($key)
    {
        $this->checkKeyExists($key);

        if ($this->config[$key] instanceof Mapper) {
            return $this->config[$key]->map();
        }

        return $this->config[$key];
    }

    /**
     * Internal helper method to execute key existence checking
     *
     * @param string $key
     *
     * @throws InvalidKeyException
     */
    private function checkKeyExists($key)
    {
        if (!$this->has($key)) {
            throw new InvalidKeyException(sprintf('No such key "%s" exists in Config.', $key));
        }
    }
}
