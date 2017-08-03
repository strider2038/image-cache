<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Validation;

use Strider2038\ImgCache\Imaging\Validation\ImageValidator;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImageValidatorTest extends FileTestCase
{
    /**
     * @param string $mimeType
     * @param bool $expectedIsValid
     * @dataProvider mimeTypeProvider
     */
    public function testIsValidImageMimeType_GivenMimeType_BoolIsReturned(string $mimeType, bool $expectedIsValid): void
    {
        $validator = $this->createImageValidator();

        $isValid = $validator->isValidImageMimeType($mimeType);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    public function mimeTypeProvider(): array
    {
        return [
            ['application/json', false],
            ['image/jpeg', true],
            ['image/png', true],
        ];
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function testHasFileValidImageMimeType_GivenFileDoesNotExist_ExceptionThrown(): void
    {
        $validator = $this->createImageValidator();

        $validator->hasFileValidImageMimeType('not.exist');
    }

    /**
     * @param string $filename
     * @param bool $expectedIsValid
     * @dataProvider mimeFileProvider
     */
    public function testHasFileValidImageMimeType_GivenFile_BoolIsReturned(string $filename, bool $expectedIsValid): void
    {
        $validator = $this->createImageValidator();

        $isValid = $validator->hasFileValidImageMimeType($filename);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    /**
     * @param string $filename
     * @param bool $expectedIsValid
     * @dataProvider mimeFileProvider
     */
    public function testHasFileValidImageMimeType_GivenBlob_BoolIsReturned(string $filename, bool $expectedIsValid): void
    {
        $validator = $this->createImageValidator();
        $blob = file_get_contents($filename);

        $isValid = $validator->hasBlobValidImageMimeType($blob);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    public function mimeFileProvider(): array
    {
        return [
            [$this->givenAssetFile(self::FILE_JSON), false],
            [$this->givenAssetFile(self::IMAGE_CAT300), true],
            [$this->givenAssetFile(self::IMAGE_RIDER_PNG), true],
        ];
    }

    /**
     * @param string $filename
     * @param bool $expectedIsValid
     * @dataProvider imageFilenamesProvider
     */
    public function testHasValidImageExtension_GivenFilename_BoolIsReturned(string $filename, bool $expectedIsValid): void
    {
        $validator = $this->createImageValidator();

        $isValid = $validator->hasValidImageExtension($filename);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    public function imageFilenamesProvider(): array
    {
        return [
            ['a.jpg', true],
            ['a.jpeg', true],
            ['a.png', true],
            ['a.PNG', false],
            ['a.exe', false],
            ['/a.php', false],
        ];
    }

    private function createImageValidator(): ImageValidator
    {
        $validator = new ImageValidator();

        return $validator;
    }
}
