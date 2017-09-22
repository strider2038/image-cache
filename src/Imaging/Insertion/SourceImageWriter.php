<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Insertion;

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageWriter implements ImageWriterInterface
{
    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    public function __construct(SourceKeyParserInterface $keyParser, SourceAccessorInterface $sourceAccessor)
    {
        $this->keyParser = $keyParser;
        $this->sourceAccessor = $sourceAccessor;
    }

    public function insert(string $key, StreamInterface $data): void
    {
        $parsedKey = $this->keyParser->parse($key);
        $this->sourceAccessor->put($parsedKey->getPublicFilename(), $data);
    }

    public function delete(string $key): void
    {
        $parsedKey = $this->keyParser->parse($key);
        $this->sourceAccessor->delete($parsedKey->getPublicFilename());
    }
}
