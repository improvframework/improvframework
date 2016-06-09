<?php

namespace Improv;

use Improv\Configuration\Exceptions\InvalidMapperClassException;
use Improv\Configuration\Mapper;
use Improv\Configuration\MapperFactory;
use Improv\Configuration\Test\AbstractTestCase;
use Improv\Configuration\Test\Fixtures\InvalidMapper;
use Improv\Configuration\Test\Fixtures\ValidMapper;

/**
 * @covers \Improv\Configuration\MapperFactory
 */
class MapperFactoryTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function defaultMapperClass()
    {
        $sut = new MapperFactory();

        self::assertInstanceOf(Mapper::class, $sut->createNew('value'));

    }

    /**
     * @test
     * @uses \Improv\Configuration\Test\Fixtures\ValidMapper
     */
    public function injectedMapperClass()
    {
        $sut = new MapperFactory(ValidMapper::class);

        self::assertInstanceOf(ValidMapper::class, $sut->createNew('value'));
    }

    /**
     * @test
     */
    public function injectedMapperDoesNotExtendMapper()
    {
        $this->expectException(InvalidMapperClassException::class);
        $this->expectExceptionMessage('Provided Mapper class "\Baloney" does not exist.');

        new MapperFactory('\Baloney');
    }

    /**
     * @test
     * @uses \Improv\Configuration\Test\Fixtures\InvalidMapper
     */
    public function injectedMapperDoesNotExist()
    {
        $this->expectException(InvalidMapperClassException::class);
        $this->expectExceptionMessage(
            'Provided Mapper class "' . InvalidMapper::class . '" must extend "' . Mapper::class . '".'
        );

        new MapperFactory(InvalidMapper::class);
    }
}
