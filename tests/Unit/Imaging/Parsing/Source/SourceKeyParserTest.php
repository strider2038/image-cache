<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Source;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKey;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParser;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

class SourceKeyParserTest extends TestCase
{
    private const INVALID_KEY = 'a';

    /** @var KeyValidatorInterface */
    private $keyValidator;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    protected function setUp()
    {
        $this->keyValidator = \Phake::mock(KeyValidatorInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp  /Invalid filename .* in request/
     */
    public function parse_givenInvalidKey_exceptionThrown(): void
    {
        $parser = $this->createSourceKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns(self::INVALID_KEY,false);

        $parser->parse(self::INVALID_KEY);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported image extension
     */
    public function parse_givenKeyHasInvalidExtension_exceptionThrown(): void
    {
        $parser = $this->createSourceKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns(self::INVALID_KEY,true);
        $this->givenImageValidator_hasValidImageExtension_returns(self::INVALID_KEY, false);

        $parser->parse(self::INVALID_KEY);
    }

    /**
     * @test
     * @param string $key
     * @param string $publicFilename
     * @dataProvider validKeyProvider
     */
    public function parse_givenKey_keyParsedToThumbnailKey(string $key, string $publicFilename): void
    {
        $parser = $this->createSourceKeyParser();
        $this->givenKeyValidator_isValidPublicFilename_returns($key,true);
        $this->givenImageValidator_hasValidImageExtension_returns($key, true);

        $thumbnailKey = $parser->parse($key);

        $this->assertInstanceOf(SourceKey::class, $thumbnailKey);
        $this->assertEquals($publicFilename, $thumbnailKey->getPublicFilename());
    }

    public function validKeyProvider(): array
    {
        return [
            ['a.jpg', 'a.jpg'],
            ['/a_q1.jpg', '/a_q1.jpg'],
            ['/b_a1_b2.png', '/b_a1_b2.png'],
            ['/a/b/c/d_q5.jpg', '/a/b/c/d_q5.jpg'],
        ];
    }

    private function createSourceKeyParser(): SourceKeyParser
    {
        return new SourceKeyParser($this->keyValidator, $this->imageValidator);
    }

    private function givenKeyValidator_isValidPublicFilename_returns(string $filename, bool $value): void
    {
        \Phake::when($this->keyValidator)->isValidPublicFilename($filename)->thenReturn($value);
    }

    private function givenImageValidator_hasValidImageExtension_returns(string $filename, bool $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension($filename)->thenReturn($value);
    }
}
