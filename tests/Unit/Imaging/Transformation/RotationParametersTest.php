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

use Strider2038\ImgCache\Imaging\Transformation\RotationParameters;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class RotationParametersTest extends TestCase
{
    private const ROTATION_PARAMETERS_ID = 'rotation parameters';

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
        $parameters = new RotationParameters(0);

        $id = $parameters->getId();

        $this->assertEquals(self::ROTATION_PARAMETERS_ID, $id);
    }

    /**
     * @test
     * @param float $degree
     * @param int $violationsCount
     * @dataProvider rotationParametersAndViolationsCountProvider
     */
    public function validate_givenDegree_violationsReturned(float $degree, int $violationsCount): void
    {
        $parameters = new RotationParameters($degree);

        $violations = $this->validator->validate($parameters);

        $this->assertCount($violationsCount, $violations);
        $this->assertEquals($degree, $parameters->getDegree());
    }

    public function rotationParametersAndViolationsCountProvider(): array
    {
        return [
            [0, 0],
            [360, 0],
            [360.1, 1],
            [-360, 0],
            [-360.1, 1],
        ];
    }
}
