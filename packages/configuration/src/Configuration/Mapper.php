<?php

namespace Improv\Configuration;

class Mapper
{

    /**
     * @var mixed
     */
    private $cached;

    /**
     * @var callable
     */
    private $func;

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function map($value)
    {
        if (isset($this->cached[$value])) {
            return $this->cached[$value];
        }

        if ($this->func === null) {
            return $this->cached[$value] = $value;
        }

        $func = $this->func;
        return $this->cached[$value] = $func($value);
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
