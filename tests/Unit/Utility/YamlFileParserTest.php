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

class YamlFileParserTest extends FileTestCase
{
    /** @test */
    public function parseConfigurationFile_givenYamlFile_fileParsedAndArrayReturned(): void
    {
        $parser = new YamlFileParser();
        $filename = $this->givenYamlFile();

        $contents = $parser->parseConfigurationFile($filename);

        $this->assertEquals(['section' => ['value']], $contents);
    }
}
