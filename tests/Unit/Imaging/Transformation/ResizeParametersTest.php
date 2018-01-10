<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Transformation\ResizeParameters;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class ResizeParametersTest extends TestCase
{
    private const RESIZE_PARAMETERS_ID = 'resize parameters';

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
        $directoryName = new ResizeParameters(0, 0, new ResizeModeEnum(ResizeModeEnum::STRETCH));

        $id = $directoryName->getId();

        $this->assertEquals(self::RESIZE_PARAMETERS_ID, $id);
    }

    /**
     * @test
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param int $violationsCount
     * @dataProvider resizeParametersAndViolationsCountProvider
     */
    public function validate_givenWidthAndHeightAndMode_violationsReturned(
        int $width,
        int $height,
        string $mode,
        int $violationsCount
    ): void {
        $resizeParameters = new ResizeParameters($width, $height, new ResizeModeEnum($mode));

        $violations = $this->validator->validate($resizeParameters);

        $this->assertCount($violationsCount, $violations);
        $this->assertEquals($width, $resizeParameters->getWidth());
        $this->assertEquals($height, $resizeParameters->getHeight());
        $this->assertEquals($mode, $resizeParameters->getMode()->getValue());
    }

    public function resizeParametersAndViolationsCountProvider(): array
    {
        return [
            [20, 20, ResizeModeEnum::FIT_IN, 0],
            [19, 20, ResizeModeEnum::FIT_IN, 1],
            [2000, 20, ResizeModeEnum::FIT_IN, 0],
            [2001, 20, ResizeModeEnum::FIT_IN, 1],
            [20, 19, ResizeModeEnum::FIT_IN, 1],
            [20, 2000, ResizeModeEnum::FIT_IN, 0],
            [20, 2001, ResizeModeEnum::FIT_IN, 1],
        ];
    }
}
