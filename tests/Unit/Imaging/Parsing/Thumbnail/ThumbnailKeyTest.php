<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Thumbnail;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKey;

class ThumbnailKeyTest extends TestCase
{
    const PUBLIC_FILENAME = 'a.jpg';
    const PROCESSING_CONFIGURATION = 'q5';

    public function testConstruct_GivenProperties_PropertiesAreSet(): void
    {
        $key = new ThumbnailKey(self::PUBLIC_FILENAME, self::PROCESSING_CONFIGURATION);

        $this->assertEquals(self::PUBLIC_FILENAME, $key->getPublicFilename());
        $this->assertEquals(self::PROCESSING_CONFIGURATION, $key->getProcessingConfiguration());
    }
}
