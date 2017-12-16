<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver\WebDAV;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceProperties;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesCollection;

class ResourcePropertiesCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenResourceProperties_collectionCreated(): void
    {
        $properties = \Phake::mock(ResourceProperties::class);

        $collection = new ResourcePropertiesCollection([$properties]);

        $this->assertCount(1, $collection);
        $this->assertSame($properties, $collection->first());
    }
}
