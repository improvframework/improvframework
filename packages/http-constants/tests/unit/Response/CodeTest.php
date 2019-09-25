<?php

namespace Improv\Http\Response;

/**
 * @coversDefaultClass \Improv\Http\Response\Code
 */
class CodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider validCodeProvider
     *
     * @covers ::isValid
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testIsValidCode($code, $expected)
    {

        $actual = Code::isValid($code);
        $this->assertTrue($expected === $actual);

    }

    /**
     * @return array
     */
    public function validCodeProvider()
    {

        return [

            [ 201, true ],
            [ 320, false ]

        ];

    }
}
