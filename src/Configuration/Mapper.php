<?php

namespace Improv\Configuration;

class Mapper
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $cached;

    /**
     * @var callable
     */
    private $func;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
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

    /**
     * @param callable $func
     *
     * @return void
     */
    public function using(callable $func)
    {
        $this->func = $func;
    }

    /**
     * @return void
     */
    public function toInt()
    {
        $this->func = function ($val) {
            return (int) $val;
        };
    }

    /**
     * @return void
     */
    public function toBool()
    {
        $this->func = function ($val) {
            return in_array(strtoupper($val), ['1', 1, 'TRUE', true], true);
        };
    }
}
