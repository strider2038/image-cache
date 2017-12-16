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

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageExtractor implements ImageExtractorInterface
{
    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        SourceKeyParserInterface $keyParser,
        StorageAccessorInterface $storageAccessor
    ) {
        $this->keyParser = $keyParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function extractImage(string $key): Image
    {
        $sourceKey = $this->keyParser->parse($key);

        return $this->storageAccessor->getImage($sourceKey->getPublicFilename());
    }
}
