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
        self::assertSame('Original Value', (new Mapper('Original Value'))->map());
    }

    /**
     * @test
     */
    public function mappingValues()
    {
        $original_value = 'baloney';
        $mapped_value   = 'YENOLAB';

        $sut            = new Mapper($original_value);

        $sut->using(function ($value) {
            return strrev(strtoupper($value));
        });

        self::assertSame($mapped_value, $sut->map());
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

        $sut = new Mapper($original_value);

        $sut->using($test_map);

        self::assertSame($mapped_value, $sut->map());
        self::assertSame($mapped_value, $sut->map());
    }

    /**
     * @test
     * @dataProvider toIntDataProvider
     */
    public function toInt($original, $expected)
    {
        $sut = new Mapper($original);
        $sut->toInt();

        self::assertSame($expected, $sut->map());
    }

    /**
     * @test
     * @dataProvider toBoolDataProvider
     */
    public function toBool($original, $expected)
    {
        $sut = new Mapper($original);
        $sut->toBool();

        self::assertSame($expected, $sut->map());
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
