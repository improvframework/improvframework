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
     * @var string
     */
    private $prefix;

    /**
     * @var MapperFactory
     */
    private $factory_mapper;

    /**
     * @var Mapper[]
     */
    private $mappers;

    /**
     * @param array              $config
     * @param string             $prefix
     * @param MapperFactory|null $mapper_factory
     */
    public function __construct(array $config, $prefix = '', MapperFactory $mapper_factory = null)
    {

        $this->config         = $config;
        $this->prefix         = $prefix;
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
     * Configures a Mapper object for the given Keys
     *
     * The Mapper object can be configured externally by being returned and, as it is stored as a reference,
     * will be shared across all "mapped" keys.
     *
     * @param string[] $keys
     * @return Mapper
     *
     * @throws InvalidKeyException
     */
    public function map(...$keys)
    {
        $mapper = $this->factory_mapper->createNew();

        array_walk($keys, function ($key) use ($mapper) {
            $this->checkKeyExists($key);
            $this->mappers[$key] = $mapper;
        });

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

        if (isset($this->mappers[$key])) {
            return $this->mappers[$key]->map($this->config[$key]);
        }

        return $this->config[$key];
    }

    /**
     * Generates a new Configuration object from the current one, applying another layer of prefix filtration.
     *
     * @param string $prefix
     *
     * @return Configuration
     */
    public function withPrefix($prefix)
    {
        $config          = new self($this->config, $prefix, $this->factory_mapper);
        $config->mappers = [];

        foreach ($this->mappers as $key => $mapper) {
            if (strpos($key, $prefix) !== 0) {
                continue;
            }
            $config->mappers[str_replace($prefix, '', $key)] = $mapper;
        }

        return $config;
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
