<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSourceCollection;

class ImageSourceCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenImageSource_imageSourceInCollection(): void
    {
        $source = \Phake::mock(AbstractImageSource::class);

        $collection = new ImageSourceCollection([$source]);

        $this->assertCount(1, $collection);
        $this->assertSame($source, $collection->first());
    }
}
