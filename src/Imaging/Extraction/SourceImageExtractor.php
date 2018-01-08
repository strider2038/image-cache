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
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageExtractor implements ImageExtractorInterface
{
    /** @var PlainFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        PlainFilenameParserInterface $filenameParser,
        StorageAccessorInterface $storageAccessor
    ) {
        $this->filenameParser = $filenameParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function getProcessedImage(string $filename): Image
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);

        return $this->storageAccessor->getImage($parsedFilename->getValue());
    }
}
