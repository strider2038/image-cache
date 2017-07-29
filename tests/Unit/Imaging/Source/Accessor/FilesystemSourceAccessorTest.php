<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Accessor;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\FilesystemSourceAccessor;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class FilesystemSourceAccessorTest extends TestCase
{
    use ImageTrait, ProviderTrait;

    const KEY = 'test';

    /** @var FilesystemSourceInterface */
    private $source;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    protected function setUp()
    {
        $this->source = \Phake::mock(FilesystemSourceInterface::class);
        $this->keyMapper = \Phake::mock(FilenameKeyMapperInterface::class);
    }

    public function testGet_GivenKeyAndSourceFileDoesNotExist_NullIsReturned(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_GetKey_ReturnsFilenameKey(self::KEY);
        $this->givenSource_Get_Returns($filenameKey, null);

        $image = $accessor->get(self::KEY);

        $this->assertNull($image);
    }

    public function testGet_GivenKeyAndSourceFileExists_ImageIsReturned(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_GetKey_ReturnsFilenameKey(self::KEY);
        $sourceImage = $this->givenImage();
        $this->givenSource_Get_Returns($filenameKey, $sourceImage);

        $image = $accessor->get(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertSame($sourceImage, $image);
    }

    /**
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function testGet_GivenKeyAndSourceFileExistStatus_BoolIsReturned(bool $expectedExists): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_GetKey_ReturnsFilenameKey(self::KEY);
        $this->givenSource_Exists_Returns($filenameKey, $expectedExists);

        $actualExists = $accessor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    private function createFilesystemSourceAccessor(): FilesystemSourceAccessor
    {
        $accessor = new FilesystemSourceAccessor($this->source, $this->keyMapper);

        return $accessor;
    }

    private function givenKeyMapper_GetKey_ReturnsFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($this->keyMapper)->getKey($filename)->thenReturn($filenameKey);

        return $filenameKey;
    }

    private function givenSource_Get_Returns(FilenameKeyInterface $filenameKey, ?ImageInterface $image): void
    {
        \Phake::when($this->source)->get($filenameKey)->thenReturn($image);
    }

    private function givenSource_Exists_Returns(FilenameKeyInterface $filenameKey, bool $value): void
    {
        \Phake::when($this->source)->exists($filenameKey)->thenReturn($value);
    }
}
