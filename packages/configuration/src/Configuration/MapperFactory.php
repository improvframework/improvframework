<?php

namespace Improv\Configuration;

use Improv\Configuration\Exceptions\InvalidMapperClassException;

class MapperFactory
{
    /**
     * @var string
     */
    private $mapper_class;

    /**
     * @param string $mapper_class
     *
     * @throws InvalidMapperClassException
     */
    public function __construct($mapper_class = Mapper::class)
    {
        $this->mapper_class = $mapper_class;

        if ($mapper_class === Mapper::class) {
            return;
        }

        if (!class_exists($mapper_class)) {
            throw new InvalidMapperClassException(
                sprintf('Provided Mapper class "%s" does not exist.', $mapper_class)
            );
        }

        if (!is_subclass_of($mapper_class, Mapper::class)) {
            throw new InvalidMapperClassException(
                sprintf('Provided Mapper class "%s" must extend "%s".', $mapper_class, Mapper::class)
            );
        }
    }

    /**
     * @param mixed $value
     *
     * @return Mapper
     */
    public function createNew()
    {
        return new $this->mapper_class();
    }
}
