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
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\GeoMapStorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapExtractor implements ImageExtractorInterface
{
    /** @var GeoMapParametersParserInterface */
    private $parametersParser;

    /** @var GeoMapStorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        GeoMapParametersParserInterface $parametersParser,
        GeoMapStorageAccessorInterface $storageAccessor
    ) {
        $this->parametersParser = $parametersParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function getProcessedImage(string $filename): Image
    {
        $parameters = $this->parametersParser->parseMapParametersFromFilename($filename);

        return $this->storageAccessor->getImage($parameters);
    }
}
