<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Data;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameFactory;

class StorageFilenameFactoryTest extends TestCase
{
    private const FILENAME = '/file.ext';

    /** @test */
    public function createStorageFilename_givenFilename_filenameEqualsToReturnedFilenameKeyValue(): void
    {
        $factory = new StorageFilenameFactory();

        $storageFilename = $factory->createStorageFilename(self::FILENAME);

        $this->assertEquals(self::FILENAME, $storageFilename->getValue());
    }
}
