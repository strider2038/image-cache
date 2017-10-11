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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Tests\Support\AnnotatedModelMock;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ModelValidatorTest extends TestCase
{
    /** @test */
    public function validate_givenModelWithViolations_violationsIsReturned(): void
    {
        $validator = new ModelValidator();
        $model = new AnnotatedModelMock();

        $violations = $validator->validate($model);

        $this->assertCount(1, $violations);
        /** @var ConstraintViolationInterface $violation */
        $violation = $violations->get(0);
        $this->assertEquals('property', $violation->getPropertyPath());
        $this->assertRegExp('/.*not be blank.*/i', $violation->getMessage());
    }
}
