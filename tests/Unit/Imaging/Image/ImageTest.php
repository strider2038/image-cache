<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image;

use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class ImageTest extends FileTestCase
{
    private const IMAGE_ID = 'image';

    /** @var ImageParameters */
    private $parameters;

    /** @var StreamInterface */
    private $data;

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->parameters = \Phake::mock(ImageParameters::class);
        $this->data = \Phake::mock(StreamInterface::class);
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $image = new Image($this->data, $this->parameters);

        $id = $image->getId();

        $this->assertEquals(self::IMAGE_ID, $id);
    }

    /** @test */
    public function construct_givenImageParametersAndData_ImageParametersAndDataAreAccessible(): void
    {
        $image = new Image($this->data, $this->parameters);

        $this->assertSame($this->parameters, $image->getParameters());
        $this->assertSame($this->data, $image->getData());
    }

    /** @test */
    public function setParameters_givenParameters_ParametersAreSet(): void
    {
        $parameters = \Phake::mock(ImageParameters::class);
        $image = new Image($this->data, $this->parameters);

        $image->setParameters($parameters);

        $this->assertSame($parameters, $image->getParameters());
    }

    /** @test */
    public function validate_givenValidImage_noViolationsReturned(): void
    {
        $stream = $this->givenFileStream(self::IMAGE_BOX_JPG);
        $image = new Image($stream, $this->parameters);

        $violations = $this->validator->validate($image);

        $this->assertCount(0, $violations);
    }

    /** @test */
    public function validate_givenInvalidImage_violationsReturned(): void
    {
        $stream = $this->givenFileStream(self::FILE_JSON);
        $image = new Image($stream, $this->parameters);

        $violations = $this->validator->validate($image);

        $this->assertCount(1, $violations);
    }

    private function givenFileStream(string $assetFilename): ResourceStream
    {
        $image = $this->givenAssetFilename($assetFilename);
        $resource = fopen($image, ResourceStreamModeEnum::READ_ONLY);

        return new ResourceStream($resource);
    }
}
