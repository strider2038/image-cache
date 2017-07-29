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
        $publicFilename = $this->getPublicFilename($key);

        return $this->sourceAccessor->get($publicFilename);
    }

    public function exists(string $key): bool
    {
        $publicFilename = $this->getPublicFilename($key);

        return $this->sourceAccessor->exists($publicFilename);
    }

    private function getPublicFilename(string $key): string
    {
        /** @var SourceKeyInterface $sourceKey */
        $sourceKey = $this->keyParser->parse($key);

        $publicFilename = $sourceKey->getPublicFilename();

        return $publicFilename;
    }
}