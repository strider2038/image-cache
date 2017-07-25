<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing;

use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequest;
use Strider2038\ImgCache\Imaging\Extraction\Request\RequestConfiguration;
use Strider2038\ImgCache\Imaging\Extraction\Request\RequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParser implements ThumbnailKeyParserInterface
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    public function __construct(TransformationsFactoryInterface $transformationsFactory)
    {
        $this->transformationsFactory = $transformationsFactory;
    }

    public function getRequestConfiguration(string $filename): RequestConfigurationInterface
    {
        if (!preg_match('/^[A-Za-z0-9_\.\/]+$/', $filename) || $filename === '/') {
            throw new InvalidImageException(
                "Requested filename '{$filename}' contains illegal "
                . "characters or is empty"
            );
        }
        if (substr($filename, 0, 1) === '/') {
            $filename = substr($filename, 1);
        }

        $path = pathinfo($filename);
        if (!in_array(strtolower($path['extension']), static::getSupportedExtensions())) {
            throw new InvalidImageException(
                "Filename extension '{$path['extension']}' is not supported"
            );
        }

        $dirname = $path['dirname'] === '.' ? '' : "/{$path['dirname']}";
        foreach (explode('/', $dirname) as $dirpart) {
            if (strpos($dirpart, '.') !== false) {
                throw new InvalidImageException("Dots are not allowed in directory names");
            }
        }

        $filenameParts = explode('_', $path['filename']);
        $filenamePartsCount = count($filenameParts);

        $transformations = new TransformationsCollection();
        if ($filenamePartsCount > 1) {
            for ($i = 1; $i < $filenamePartsCount; $i++) {
                $transformation = $this->transformationsFactory->create($filenameParts[$i]);
                $transformations->add($transformation);
            }
        }

        $extractionRequest = new FileExtractionRequest(
            "{$dirname}/{$filenameParts[0]}.{$path['extension']}"
        );

        $requestConfiguration = new RequestConfiguration($extractionRequest);
        $requestConfiguration->setTransformations($transformations);

        return $requestConfiguration;
    }

    public static function getSupportedExtensions(): array
    {
        return ['jpg', 'jpeg'];
    }
}