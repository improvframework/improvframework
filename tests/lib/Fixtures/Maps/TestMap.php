<?php

namespace Improv\Configuration\Test\Fixtures\Maps;

class TestMap
{
    public function __invoke($value)
    {
        return $value;
    }
}
