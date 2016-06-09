<?php

namespace Improv\Configuration;

class MapperFactory
{
    public function createNew($value)
    {
        return new Mapper($value);
    }
}
