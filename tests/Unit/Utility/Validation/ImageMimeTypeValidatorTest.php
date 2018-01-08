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
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;
use Strider2038\ImgCache\Utility\Validation\ImageMimeType;
use Strider2038\ImgCache\Utility\Validation\ImageMimeTypeValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ImageMimeTypeValidatorTest extends TestCase
{
    private const VALID_MIME_TYPE = 'valid_mime_type';
    private const INVALID_MIME_TYPE = 'invalid_mime_type';

    /** @var MetadataReaderInterface */
    private $metadataReader;

    protected function setUp(): void
    {
        $this->metadataReader = \Phake::mock(MetadataReaderInterface::class);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function validate_givenInvalidInstanceOfValue_unexpectedTypeExceptionThrown(): void
    {
        $context = $this->givenExecutionContext();
        $validator = $this->createImageMimeTypeValidator($context);
        $constraint = new ImageMimeType();

        $validator->validate('', $constraint);
    }

    /** @test */
    public function validate_givenValidMimeType_noViolationsAdded(): void
    {
        $context = $this->givenExecutionContext();
        $validator = $this->createImageMimeTypeValidator($context);
        $constraint = $this->givenImageMimeTypeConstraint();
        $stream = \Phake::mock(StreamInterface::class);
        $this->givenMetadataReader_getContentTypeFromStream_returnsMimeType(self::VALID_MIME_TYPE);

        $validator->validate($stream, $constraint);

        $this->assertMetadataReader_getContentTypeFromStream_isCalledOnceWithStream($stream);
        \Phake::verifyNoInteraction($context);
    }

    /** @test */
    public function validate_givenInvalidMimeType_violationsAdded(): void
    {
        $context = $this->givenExecutionContext();
        $validator = $this->createImageMimeTypeValidator($context);
        $constraint = $this->givenImageMimeTypeConstraint();
        $stream = \Phake::mock(StreamInterface::class);
        $this->givenMetadataReader_getContentTypeFromStream_returnsMimeType(self::INVALID_MIME_TYPE);
        $violationBuilder = $this->givenContext_buildViolation_returnsViolationBuilder($context);

        $validator->validate($stream, $constraint);

        $this->assertMetadataReader_getContentTypeFromStream_isCalledOnceWithStream($stream);
        $this->assertContext_buildViolation_isCalledOnceWithMessage($context, $constraint->message);
        $this->assertViolationBuilder_addViolation_isCalledOnce($violationBuilder);
    }

    private function createImageMimeTypeValidator(ExecutionContext $context): ImageMimeTypeValidator
    {
        $validator = new ImageMimeTypeValidator($this->metadataReader);
        $validator->initialize($context);

        return $validator;
    }

    private function givenImageMimeTypeConstraint(): ImageMimeType
    {
        return new ImageMimeType([
            'mimeTypes' => [self::VALID_MIME_TYPE]
        ]);
    }

    private function assertMetadataReader_getContentTypeFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->metadataReader, \Phake::times(1))->getContentTypeFromStream($stream);
    }

    private function givenMetadataReader_getContentTypeFromStream_returnsMimeType(string $mimeType): void
    {
        \Phake::when($this->metadataReader)->getContentTypeFromStream(\Phake::anyParameters())->thenReturn($mimeType);
    }

    private function givenExecutionContext(): ExecutionContext
    {
        return \Phake::mock(ExecutionContext::class);
    }

    private function givenContext_buildViolation_returnsViolationBuilder(
        ExecutionContext $context
    ): ConstraintViolationBuilderInterface {
        $violationBuilder = \Phake::mock(ConstraintViolationBuilderInterface::class);
        \Phake::when($context)->buildViolation(\Phake::anyParameters())->thenReturn($violationBuilder);

        return $violationBuilder;
    }

    private function assertContext_buildViolation_isCalledOnceWithMessage(ExecutionContext $context, string $message): void
    {
        \Phake::verify($context, \Phake::times(1))->buildViolation($message);
    }

    private function assertViolationBuilder_addViolation_isCalledOnce(ConstraintViolationBuilderInterface $violationBuilder): void
    {
        \Phake::verify($violationBuilder, \Phake::times(1))->addViolation(\Phake::anyParameters());
    }
}
