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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class ImageParametersTest extends TestCase
{
    private const IMAGE_PARAMETERS_ID = 'image parameters';
    private const QUALITY_VALUE_DEFAULT = 85;

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
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
        $image = new ImageParameters();

        $id = $image->getId();

        $this->assertEquals(self::IMAGE_PARAMETERS_ID, $id);
    }

    /** @test */
    public function getQuality_classConstructed_returnedValuesHasDefaultValue(): void
    {
        $parameters = new ImageParameters();

        $result = $parameters->getQuality();

        $this->assertEquals(self::QUALITY_VALUE_DEFAULT, $result);
    }

    /**
     * @test
     * @param int $quality
     * @param int $violationsCount
     * @dataProvider qualityAndViolationsCountProvider
     */
    public function validate_givenQuality_violationsReturned(int $quality, int $violationsCount): void
    {
        $parameters = new ImageParameters();
        $parameters->setQuality($quality);

        $violations = $this->validator->validate($parameters);

        $this->assertCount($violationsCount, $violations);
    }

    public function qualityAndViolationsCountProvider(): array
    {
        return [
            [15, 0],
            [100, 0],
            [14, 1],
            [101, 1],
        ];
    }
}
