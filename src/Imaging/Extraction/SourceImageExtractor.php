<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageExtractor implements ImageExtractorInterface
{
    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    public function __construct(
        SourceKeyParserInterface $keyParser,
        SourceAccessorInterface $sourceAccessor
    ) {
        $this->keyParser = $keyParser;
        $this->sourceAccessor = $sourceAccessor;
    }

    public function extract(string $key): ?ImageInterface
    {
        $sourceFilename = $this->getSourceFilename($key);

        return $this->sourceAccessor->get($sourceFilename);
    }

    public function exists(string $key): bool
    {
        $sourceFilename = $this->getSourceFilename($key);

        return $this->sourceAccessor->exists($sourceFilename);
    }

    private function getSourceFilename(string $key): string
    {
        /** @var SourceKeyInterface $thumbnailKey */
        $thumbnailKey = $this->keyParser->parse($key);

        $sourceFilename = $thumbnailKey->getSourceFilename();

        return $sourceFilename;
    }
}