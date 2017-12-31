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
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParser;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class PlainFilenameParserTest extends TestCase
{
    private const FILENAME = 'filename';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /** @test */
    public function getParsedFilename_givenFilename_parsedFilenameReturned(): void
    {
        $parser = $this->createPlainFilenameParser();

        $plainFilename = $parser->getParsedFilename(self::FILENAME);

        $this->assertInstanceOf(PlainFilename::class, $plainFilename);
        $this->assertEquals(self::FILENAME, $plainFilename->getValue());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            PlainFilename::class,
            InvalidRequestValueException::class
        );
    }

    private function createPlainFilenameParser(): PlainFilenameParser
    {
        return new PlainFilenameParser($this->validator);
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
