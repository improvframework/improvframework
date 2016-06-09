<?php

namespace Improv\Configuration;

class Mapper
{
    private $value;
    private $func;
    private $cached;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function map()
    {

        if ($this->cached !== null) {
            return $this->cached;
        }

        if ($this->func === null) {
            return $this->cached = $this->value;
        }

        $func = $this->func;
        return $this->cached = $func($this->value);
    }

    public function using(callable $func)
    {
        $this->func = $func;
    }

    public function toInt()
    {
        $this->func = function ($val) {
            return (int) $val;
        };
    }
}
