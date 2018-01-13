<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationFactoryMap;

class TransformationFactoryMapTest extends TestCase
{
    /** @test */
    public function construct_givenTransformationFactory_oneTransformationFactoryInCollection(): void
    {
        $factory = \Phake::mock(TransformationFactoryInterface::class);

        $collection = new TransformationFactoryMap([$factory]);

        $this->assertCount(1, $collection);
    }
}
