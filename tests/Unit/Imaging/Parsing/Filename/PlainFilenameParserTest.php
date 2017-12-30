<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Filename;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParser;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PlainFilenameParserTest extends TestCase
{
    private const FILENAME = 'filename';

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationsFormatter;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->violationsFormatter = \Phake::mock(ViolationFormatterInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Filename is not valid.* /
     */
    public function getParsedFilename_givenInvalidFilename_exceptionThrown(): void
    {
        $parser = $this->createPlainFilenameParser();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 1);

        $parser->getParsedFilename(self::FILENAME);
    }

    /** @test */
    public function getParsedFilename_givenFilename_parsedFilenameReturned(): void
    {
        $parser = $this->createPlainFilenameParser();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);

        $plainFilename = $parser->getParsedFilename(self::FILENAME);

        $this->assertInstanceOf(PlainFilename::class, $plainFilename);
        $this->assertEquals(self::FILENAME, $plainFilename->getValue());
        $this->assertModelValidator_validate_isCalledOnceWithInstanceOfPlainFilename();
    }

    private function createPlainFilenameParser(): PlainFilenameParser
    {
        return new PlainFilenameParser($this->validator, $this->violationsFormatter);
    }

    private function givenValidator_validate_returnViolations(): ConstraintViolationListInterface
    {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validate(\Phake::anyParameters())->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returnsCount(ConstraintViolationListInterface $violations, int $count): void
    {
        \Phake::when($violations)->count()->thenReturn($count);
    }

    private function assertModelValidator_validate_isCalledOnceWithInstanceOfPlainFilename(): void
    {
        \Phake::verify($this->validator, \Phake::times(1))->validate(\Phake::capture($model));
        $this->assertInstanceOf(PlainFilename::class, $model);
    }
}
