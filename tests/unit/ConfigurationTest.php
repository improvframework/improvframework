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
        $factory        = $this->createMock(MapperFactory::class);
        $mapper         = $this->createMock(Mapper::class);

        $key            = 'BALONEY';
        $original_value = 'Original Value';
        $mapped_value   = 'Mapped Value';

        $factory
            ->method('createNew')
            ->with($original_value)
            ->willReturn($mapper);

        $mapper
            ->method('map')
            ->willReturn($mapped_value);

        $sut = new Configuration([$key => $original_value], '', $factory);

        self::assertSame($mapper, $sut->map($key));
        self::assertSame($mapped_value, $sut->get($key));
    }
}
