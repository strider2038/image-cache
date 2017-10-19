<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Validation\Constraints;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Validation\Constraints\ListElementsInListConstraint;
use Strider2038\ImgCache\Imaging\Validation\Constraints\ListElementsInListConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ListElementsInListConstraintValidatorTest extends TestCase
{
    private const VALUE_A = 'a';
    private const VALUE_B = 'b';

    /**
     * @test
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function validate_givenInvalidType_exceptionThrown(): void
    {
        $constraint = \Phake::mock(ListElementsInListConstraint::class);
        $validator = new ListElementsInListConstraintValidator();

        $validator->validate('', $constraint);
    }

    /** @test */
    public function validate_givenValidValues_noViolationsCreated(): void
    {
        $constraint = new ListElementsInListConstraint([
            'value' => [self::VALUE_A]
        ]);
        $context = $this->givenContext();
        $validator = new ListElementsInListConstraintValidator();
        $validator->initialize($context);

        $validator->validate(new StringList([self::VALUE_A]), $constraint);

        \Phake::verifyNoInteraction($context);
    }

    /** @test */
    public function validate_givenInvalidValues_violationsCreated(): void
    {
        $constraint = new ListElementsInListConstraint([
            'value' => [self::VALUE_B]
        ]);
        $context = $this->givenContext();
        $violationBuilder = $this->givenContext_buildViolation_returnsViolationBuilder($context);
        $validator = new ListElementsInListConstraintValidator();
        $validator->initialize($context);

        $validator->validate(new StringList([self::VALUE_A]), $constraint);

        \Phake::verify($context, \Phake::times(1))->buildViolation(\Phake::anyParameters());
        \Phake::verify($violationBuilder, \Phake::times(2))->setParameter(\Phake::anyParameters());
        \Phake::verify($violationBuilder, \Phake::times(1))->addViolation(\Phake::anyParameters());
    }

    private function givenContext(): ExecutionContext
    {
        return \Phake::mock(ExecutionContext::class);
    }

    private function givenContext_buildViolation_returnsViolationBuilder(
        ExecutionContext $context
    ): ConstraintViolationBuilderInterface {
        $violationBuilder = \Phake::mock(ConstraintViolationBuilderInterface::class);
        \Phake::when($context)->buildViolation(\Phake::anyParameters())->thenReturn($violationBuilder);
        \Phake::when($violationBuilder)->setParameter(\Phake::anyParameters())->thenReturn($violationBuilder);

        return $violationBuilder;
    }
}
