<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\AbstractClassCollection;
use Strider2038\ImgCache\Tests\Support\DummyClass;

class AbstractClassCollectionTest extends TestCase
{
    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessageRegExp /Collection element class .* does not exist/
     */
    public function construct_givenInvalidClassName_exceptionThrown(): void
    {
        new class ([]) extends AbstractClassCollection {
            protected function getElementClassName(): string
            {
                return '';
            }
        };
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Collection element must be instance of
     */
    public function construct_givenInvalidClassInstance_exceptionThrown(): void
    {
        new class (['']) extends AbstractClassCollection {
            protected function getElementClassName(): string
            {
                return self::class;
            }
        };
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Collection element must be instance of
     */
    public function set_givenInvalidClassInstance_exceptionThrown(): void
    {
        $collection = $this->createCollection();

        $collection->set(0, '');
    }

    /** @test */
    public function set_givenValidClassInstance_classInCollection(): void
    {
        $collection = $this->createCollection();

        $collection->set(0, new DummyClass());

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(DummyClass::class, $collection->first());
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Collection element must be instance of
     */
    public function add_givenInvalidClassInstance_exceptionThrown(): void
    {
        $collection = $this->createCollection();

        $collection->add('');
    }

    /** @test */
    public function add_givenValidClassInstance_classInCollection(): void
    {
        $collection = $this->createCollection();

        $collection->add(new DummyClass());

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(DummyClass::class, $collection->first());
    }

    /** @test */
    public function append_givenCollection_collectionMergedWithGivenCollection(): void
    {
        $collection = $this->createCollection();
        $collection->set('first', new DummyClass());
        $mergingCollection = $this->createCollection();
        $mergingCollection->set('second', new DummyClass());

        $collection->merge($mergingCollection);

        $this->assertCount(2, $collection);
        $this->assertTrue($collection->containsKey('first'));
        $this->assertTrue($collection->containsKey('second'));
    }

    /** @test */
    public function merge_givenCollection_collectionAppendedToGivenCollection(): void
    {
        $collection = $this->createCollection();
        $collection->add(new DummyClass());
        $appendingCollection = $this->createCollection();
        $appendingCollection->add(new DummyClass());

        $collection->append($appendingCollection);

        $this->assertCount(2, $collection);
    }

    private function createCollection(): AbstractClassCollection
    {
        return new class ([]) extends AbstractClassCollection {
            protected function getElementClassName(): string
            {
                return DummyClass::class;
            }
        };
    }
}
