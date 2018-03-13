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

use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Utility\YamlFileParser;
use Symfony\Component\Config\FileLocatorInterface;

class YamlFileParserTest extends FileTestCase
{
    private const FILENAME = 'filename';

    /** @var FileLocatorInterface */
    private $fileLocator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileLocator = \Phake::mock(FileLocatorInterface::class);
    }

    /** @test */
    public function parseConfigurationFile_givenYamlFile_fileParsedAndArrayReturned(): void
    {
        $parser = new YamlFileParser($this->fileLocator);
        $absoluteFilename = $this->givenYamlFile();
        $this->givenFileLocator_locate_returnsAbsoluteFilename($absoluteFilename);

        $contents = $parser->parseConfigurationFile(self::FILENAME);

        $this->assertFileLocator_locate_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEquals(['section' => ['value']], $contents);
    }

    /** @test */
    public function parseConfigurationFile_givenEmptyYamlFile_emptyArrayReturned(): void
    {
        $parser = new YamlFileParser($this->fileLocator);
        $absoluteFilename = $this->givenEmptyYamlFile();
        $this->givenFileLocator_locate_returnsAbsoluteFilename($absoluteFilename);

        $contents = $parser->parseConfigurationFile(self::FILENAME);

        $this->assertFileLocator_locate_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEmpty($contents);
    }

    private function givenEmptyYamlFile(): string
    {
        $absoluteFilename = $this->givenYamlFile();
        file_put_contents($absoluteFilename, '');

        return $absoluteFilename;
    }

    private function givenFileLocator_locate_returnsAbsoluteFilename(string $absoluteFilename): void
    {
        \Phake::when($this->fileLocator)->locate(\Phake::anyParameters())->thenReturn($absoluteFilename);
    }

    private function assertFileLocator_locate_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->fileLocator, \Phake::times(1))->locate($filename);
    }
}
