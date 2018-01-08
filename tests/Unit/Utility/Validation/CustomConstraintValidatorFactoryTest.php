<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility\Validation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\Validation\ImageMimeType;
use Strider2038\ImgCache\Utility\Validation\ImageMimeTypeValidator;

class CustomConstraintValidatorFactoryTest extends TestCase
{
    /** @var MetadataReaderInterface */
    private $metadataReader;

    protected function setUp(): void
    {
        $this->metadataReader = \Phake::mock(MetadataReaderInterface::class);
    }

    /** @test */
    public function getInstance_givenImageMimeTypeConstraint_imageMimeTypeValidatorInstanceReturned(): void
    {
        $factory = new CustomConstraintValidatorFactory($this->metadataReader);
        $constraint = new ImageMimeType();

        $validator = $factory->getInstance($constraint);

        $this->assertInstanceOf(ImageMimeTypeValidator::class, $validator);
    }
}
