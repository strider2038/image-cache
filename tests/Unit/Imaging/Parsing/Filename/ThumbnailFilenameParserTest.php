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
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParser;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class ThumbnailFilenameParserTest extends TestCase
{
    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /**
     * @test
     * @param string $key
     * @param string $publicFilename
     * @param string $thumbnailMask
     * @param string $processingConfiguration
     * @dataProvider filenameAndThumbnailFilenameParametersProvider
     */
    public function parse_givenKey_keyParsedToThumbnailKey(
        string $key,
        string $publicFilename,
        string $thumbnailMask,
        string $processingConfiguration
    ): void {
        $parser = $this->createThumbnailFilenameParser();

        $filename = $parser->getParsedFilename($key);

        $this->assertInstanceOf(ThumbnailFilename::class, $filename);
        $this->assertEquals($publicFilename, $filename->getValue());
        $this->assertEquals($thumbnailMask, $filename->getMask());
        $this->assertEquals($processingConfiguration, $filename->getProcessingConfiguration());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            ThumbnailFilename::class,
            InvalidRequestValueException::class
        );
    }

    public function filenameAndThumbnailFilenameParametersProvider(): array
    {
        return [
            ['a', 'a.', 'a*.', ''],
            ['a.jpg', 'a.jpg', 'a*.jpg', ''],
            ['/a_q1.jpg', '/a.jpg', '/a*.jpg', 'q1'],
            ['/b_a1_b2.png', '/b.png', '/b*.png', 'a1_b2'],
            ['/a/b/c/d_q5.jpg', '/a/b/c/d.jpg', '/a/b/c/d*.jpg', 'q5'],
        ];
    }

    private function createThumbnailFilenameParser(): ThumbnailFilenameParser
    {
        return new ThumbnailFilenameParser($this->validator);
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
