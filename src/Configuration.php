<?php

namespace Improv;

use Improv\Configuration\Mapper;
use Improv\Configuration\MapperFactory;

class Configuration
{
    private $config;
    private $factory_mapper;

    public function __construct(array $config, $prefix = '', MapperFactory $mapper_factory = null)
    {
        $filtered = array_filter($config, function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);

        $keys = array_map(
            function ($key) use ($prefix) {
                return str_replace($prefix, '', $key);
            },
            array_keys($filtered)
        );

        $this->config = array_combine($keys, array_values($filtered));

        if (!$mapper_factory) {
            $this->factory_mapper = new MapperFactory();
        }

    }

    public function has($key)
    {
        return isset($this->config[ $key ]);
    }

    public function map($key)
    {
        $this->checkKeyExists($key);

        $mapper = $this->factory_mapper->createNew($this->config[$key]);

        $this->config[$key] = $mapper;

        return $mapper;
    }

    public function get($key)
    {
        $this->checkKeyExists($key);

        if ($this->config[$key] instanceof Mapper) {
            return $this->config[$key]->map();
        }

        return $this->config[$key];
    }

    private function checkKeyExists($key)
    {
        if (!$this->has($key)) {
            throw new \OutOfBoundsException(sprintf('No such key "%s" exists in Config.', $key));
        }
    }
}
