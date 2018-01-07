<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Tests\Support\AnnotatedEntityMock;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatterInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class EntityValidatorTest extends TestCase
{
    /** @var CustomConstraintValidatorFactory */
    private $validatorFactory;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    protected function setUp(): void
    {
        $metadataReader = \Phake::mock(MetadataReaderInterface::class);
        $this->validatorFactory = new CustomConstraintValidatorFactory($metadataReader);
        $this->violationFormatter = \Phake::mock(ViolationFormatterInterface::class);
    }

    /** @test */
    public function validate_givenModelWithViolations_violationsReturned(): void
    {
        $validator = $this->createEntityValidator();
        $entity = new AnnotatedEntityMock();

        $violations = $validator->validate($entity);

        $this->assertCount(1, $violations);
        /** @var ConstraintViolationInterface $violation */
        $violation = $violations->get(0);
        $this->assertEquals('property', $violation->getPropertyPath());
        $this->assertRegExp('/.*not be blank.*/i', $violation->getMessage());
    }

    /** @test */
    public function validateWithException_givenModelWithoutViolations_noExceptionThrown(): void
    {
        $validator = $this->createEntityValidator();
        $entity = new AnnotatedEntityMock();
        $entity->setProperty('value');

        $validator->validateWithException($entity, '');

        \Phake::verifyNoInteraction($this->violationFormatter);
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Given invalid annotated entity mock
     */
    public function validateWithException_givenModelWithViolations_exceptionThrown(): void
    {
        $validator = $this->createEntityValidator();
        $entity = new AnnotatedEntityMock();

        $validator->validateWithException($entity, \DomainException::class);
    }

    private function createEntityValidator(): EntityValidator
    {
        return new EntityValidator($this->validatorFactory, $this->violationFormatter);
    }
}
