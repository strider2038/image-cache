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

class AbstractClassCollectionTest extends TestCase
{
    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessageRegExp /Class .* does not exist/
     */
    public function construct_givenInvalidClassName_exceptionThrown(): void
    {
        new class ([], '') extends AbstractClassCollection {};
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Collection element must be instance of
     */
    public function construct_givenInvalidClassInstance_exceptionThrown(): void
    {
        new class ([''], self::class) extends AbstractClassCollection {};
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

        $collection->set(0, new static());

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(self::class, $collection->first());
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

        $collection->add(new static());

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(self::class, $collection->first());
    }

    private function createCollection(): AbstractClassCollection
    {
        return new class ([], self::class) extends AbstractClassCollection {};
    }
}
