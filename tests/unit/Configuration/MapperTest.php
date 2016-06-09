<?php

namespace Improv;

use Hoa\Iterator\Recursive\Map;
use Improv\Configuration\Mapper;
use Improv\Configuration\Test\AbstractTestCase;
use Improv\Configuration\Test\Fixtures\Maps\TestMap;

/**
 * @covers \Improv\Configuration\Mapper
 */
class MapperTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function returnOriginalValueIfNoFunction()
    {
        self::assertSame('Original Value', (new Mapper())->map('Original Value'));
    }

    /**
     * @test
     */
    public function mappingValues()
    {
        $original_value = 'baloney';
        $mapped_value   = 'YENOLAB';

        $sut            = new Mapper();

        $sut->using(function ($value) {
            return strrev(strtoupper($value));
        });

        self::assertSame($mapped_value, $sut->map($original_value));
    }

    /**
     * @test
     */
    public function mappedValuesAreCached()
    {
        $original_value = 'Original Value';
        $mapped_value   = 'Mapped Value';
        $test_map       = $this->createMock(TestMap::class);

        // Once verifies the usage of instance cache
        $test_map->expects(self::once())
            ->method('__invoke')
            ->with($original_value)
            ->willReturn($mapped_value);

        $sut = new Mapper();

        $sut->using($test_map);

        self::assertSame($mapped_value, $sut->map($original_value));
        self::assertSame($mapped_value, $sut->map($original_value));
    }

    /**
     * @test
     * @dataProvider toIntDataProvider
     */
    public function toInt($original, $expected)
    {
        $sut = new Mapper();
        $sut->toInt();

        self::assertSame($expected, $sut->map($original));
    }

    /**
     * @test
     * @dataProvider toBoolDataProvider
     */
    public function toBool($original, $expected)
    {
        $sut = new Mapper();
        $sut->toBool();

        self::assertSame($expected, $sut->map($original));
    }

    /**
     * @return array
     */
    public function toIntDataProvider()
    {
        return [
            [ '1234',   1234 ],
            [ '12.34',  12 ],
            [ ' 12.3 ', 12 ],
        ];
    }

    /**
     * @return array
     */
    public function toBoolDataProvider()
    {
        return [
            [ 'true',  true ],
            [ 'false', false ],
            [ true,    true ],
            [ false,   false ],
            [ '1',     true ],
            [ '0',     false ],
            [ 1,       true ],
            [ 0,       false ],
            [ 'a',     false ],
            [ 'abc',   false ],
            [ '11',    false ],
            [ 11,      false ],
        ];
    }
}
