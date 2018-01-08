<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryMap;

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
