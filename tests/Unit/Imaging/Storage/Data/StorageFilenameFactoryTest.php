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
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameFactory;

class StorageFilenameFactoryTest extends TestCase
{
    private const ROOT_DIRECTORY = '/root_directory/';
    private const FILENAME = 'file.ext';
    private const STORAGE_FILENAME = self::ROOT_DIRECTORY . self::FILENAME;

    /** @test */
    public function createStorageFilename_givenFilename_filenameEqualsToReturnedFilenameKeyValue(): void
    {
        $directory = \Phake::mock(DirectoryNameInterface::class);
        $factory = new StorageFilenameFactory($directory);
        $this->givenDirectory_toString_returnsValue($directory, self::ROOT_DIRECTORY);

        $storageFilename = $factory->createStorageFilename(self::FILENAME);

        $this->assertDirectory_toString_isCalledOnce($directory);
        $this->assertEquals(self::STORAGE_FILENAME, $storageFilename->getValue());
    }

    private function assertDirectory_toString_isCalledOnce(DirectoryNameInterface $directory): void
    {
        \Phake::verify($directory, \Phake::times(1))->__toString();
    }

    private function givenDirectory_toString_returnsValue(DirectoryNameInterface $directory, string $value): void
    {
        \Phake::when($directory)->__toString()->thenReturn($value);
    }
}
