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
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ThumbnailImageCreatorInterface */
    private $thumbnailImageCreator;

    public function __construct(
        ThumbnailFilenameParserInterface $filenameParser,
        StorageAccessorInterface $storageAccessor,
        ThumbnailImageCreatorInterface $thumbnailImageCreator
    ) {
        $this->filenameParser = $filenameParser;
        $this->storageAccessor = $storageAccessor;
        $this->thumbnailImageCreator = $thumbnailImageCreator;
    }

    public function getProcessedImage(string $filename): Image
    {
        $thumbnailFilename = $this->filenameParser->getParsedFilename($filename);
        $sourceImage = $this->storageAccessor->getImage($thumbnailFilename->getValue());

        $processingConfiguration = $thumbnailFilename->getProcessingConfiguration();
        return $this->thumbnailImageCreator->createThumbnailImageByConfiguration($sourceImage, $processingConfiguration);
    }
}
