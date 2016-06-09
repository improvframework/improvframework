<?php

namespace Improv\Configuration\Test;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{

    protected function getFullMock($class_name)
    {

        return $this->getMockBuilder($class_name)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
