<?php

namespace Improv;

use Improv\Test\AbstractTestCase;

class ConfigurationTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function basicGets()
    {
        $data = [
            'CFG_ONE'   => 'one',
            'CFG_TWO'   => 'two',
            'OTHER_ONE' => 'otherone'
        ];

        $sut_one = new Configuration($data, 'CFG_');

        self::assertSame('one', $sut_one->get('ONE'));
        self::assertSame('two', $sut_one->get('TWO'));

    }
}
