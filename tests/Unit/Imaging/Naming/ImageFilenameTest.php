<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Naming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Naming\ImageFilename;
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;

class ImageFilenameTest extends TestCase
{
    private const VALUE = 'value';

    /** @var ModelValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new ModelValidator();
    }

    /** @test */
    public function toString_givenImageFilename_valueReturned(): void
    {
        $imageFilename = new ImageFilename(self::VALUE);

        $stringValue = (string) $imageFilename;

        $this->assertEquals(self::VALUE, $stringValue);
    }

    /**
     * @test
     * @dataProvider filenameProvider
     * @param string $filename
     * @param int $violationsCount
     */
    public function validate_givenImageFilename_violationsReturned(string $filename, int $violationsCount): void
    {
        $imageFilename = new ImageFilename($filename);

        $violations = $this->validator->validateModel($imageFilename);

        $this->assertCount($violationsCount, $violations);
    }

    public function filenameProvider(): array
    {
        return [
            ['', 1],
            ['/', 2],
            ['*image.jpg', 1],
            ['/image.jpg', 1],
            ['image.dat', 1],
            ['image.jpeg', 0],
            ['Image_Jpeg-1=0+.jpg', 0],
            ['dir/name/file.png', 0],
            ['dir/name/file..png', 1],
            ['dir//name//file.png', 1],
        ];
    }
}
