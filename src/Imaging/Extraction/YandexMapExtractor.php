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
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\YandexMapStorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapExtractor implements ImageExtractorInterface
{
    /** @var YandexMapParametersParserInterface */
    private $parser;

    /** @var YandexMapStorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        YandexMapParametersParserInterface $parser,
        YandexMapStorageAccessorInterface $storageAccessor
    ) {
        $this->parser = $parser;
        $this->storageAccessor = $storageAccessor;
    }

    public function getProcessedImage(string $filename): Image
    {
        $parameters = $this->parser->parse($filename);

        return $this->storageAccessor->getImage($parameters);
    }
}
