<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

class TransformationsCollectionTest extends TestCase
{
    public function testAdd_NoTransformationsInCollection_OneTransformationInCollection(): void
    {
        $collection = new TransformationsCollection();
        $transformation = \Phake::mock(TransformationInterface::class);

        $collection->add($transformation);

        $this->assertCount(1, $collection);
    }

    public function testIterate_OneTransformationInCollection_IterationsCountIsOne(): void
    {
        $collection = new TransformationsCollection();
        $transformation = \Phake::mock(TransformationInterface::class);
        $collection->add($transformation);
        $iterationsCount = 0;

        foreach ($collection as $item) {
            $iterationsCount++;
        }

        $this->assertEquals(1, $iterationsCount);
    }
}
