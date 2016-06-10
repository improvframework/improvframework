<?php

namespace Improv;

use Improv\Configuration\Exceptions\InvalidKeyException;
use Improv\Configuration\Mapper;
use Improv\Configuration\MapperFactory;
use Improv\Configuration\Test\AbstractTestCase;

/**
 * @covers \Improv\Configuration
 */
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

        $sut_two = new Configuration($data, 'OTHER_');

        self::assertSame('otherone', $sut_two->get('ONE'));

        $sut_three = new Configuration($data);

        self::assertSame('one', $sut_three->get('CFG_ONE'));
        self::assertSame('otherone', $sut_three->get('OTHER_ONE'));
    }

    /**
     * @test
     */
    public function has()
    {
        $sut = new Configuration(['KEY' => 'value']);

        self::assertTrue($sut->has('KEY'));

        self::assertFalse($sut->has('Key'));
        self::assertFalse($sut->has('MISSING'));
    }

    /**
     * @test
     */
    public function cannotGetInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionMessage('No such key "INVALID" exists in Config.');

        $sut = new Configuration([]);

        $sut->get('INVALID');
    }

    /**
     * @test
     */
    public function cannotMapInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionMessage('No such key "INVALID" exists in Config.');

        $sut = new Configuration([]);

        $sut->map('INVALID');
    }

    /**
     * @test
     *
     * @uses \Improv\Configuration\MapperFactory
     * @uses \Improv\Configuration\Mapper
     */
    public function defaultMapper()
    {
        $sut             = new Configuration(['BALONEY' => 'Value']);
        $expected_mapper = new Mapper('Value');

        self::assertEquals($expected_mapper, $sut->map('BALONEY'));
    }

    /**
     * @test
     */
    public function mappingValues()
    {
        $factory = $this->createMock(MapperFactory::class);
        $mapper  = $this->createMock(Mapper::class);

        $factory
            ->method('createNew')
            ->willReturn($mapper);

        $mapper
            ->method('map')
            ->willReturnMap([
                [ 'Original Value 1', 'Mapped Value 1' ],
                [ 'Original Value 2', 'Mapped Value 2' ],
                [ 'Original Value 3', 'Mapped Value 3' ],
            ]);

        $config = [
            'KEY1' => 'Original Value 1',
            'KEY2' => 'Original Value 2',
            'KEY3' => 'Original Value 3',
        ];

        $sut = new Configuration($config, '', $factory);

        self::assertSame($mapper, $sut->map('KEY1', 'KEY2', 'KEY3'));

        self::assertSame('Mapped Value 1', $sut->get('KEY1'));
        self::assertSame('Mapped Value 2', $sut->get('KEY2'));
        self::assertSame('Mapped Value 3', $sut->get('KEY3'));
    }

    /**
     * @test
     */
    public function withPrefix()
    {
        $data = [
            'OUTER_PREFIX_ONE'   => 'One',
            'OUTER_PREFIX_TWO'   => 'Two',
            'OUTER_THING'        => 'Thing',
        ];

        $original = new Configuration($data, 'OUTER_');

        $original->map('PREFIX_TWO')->using(function ($val) {
            return strrev(strtoupper($val));
        });

        $original->map('THING')->using(function ($val) {
            return strrev(strtoupper($val));
        });

        self::assertSame('GNIHT', $original->get('THING'));

        $sut = $original->withPrefix('PREFIX_');

        self::assertInstanceOf(Configuration::class, $sut);

        self::assertSame('One', $sut->get('ONE'));
        self::assertSame('OWT', $sut->get('TWO'));

        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionMessage('No such key "THING" exists in Config.');

        $sut->get('THING');
    }
}
