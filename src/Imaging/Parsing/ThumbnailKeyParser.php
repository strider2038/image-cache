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
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParser implements ThumbnailKeyParserInterface
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    public function __construct(
        TransformationsFactoryInterface $transformationsFactory,
        SaveOptionsConfiguratorInterface $saveOptionsConfigurator
    )
    {
        $this->transformationsFactory = $transformationsFactory;
        $this->saveOptionsConfigurator = $saveOptionsConfigurator;
    }

    public function getRequestConfiguration(string $filename): RequestConfigurationInterface
    {
        $path = $this->extractPath($filename);
        $directory = $this->extractDirectory($path);

        $filenameParts = explode('_', $path['filename']);

        $extractionRequest = new FileExtractionRequest(
            "{$directory}/{$filenameParts[0]}.{$path['extension']}"
        );

        $requestConfiguration = new RequestConfiguration($extractionRequest);

        [$transformations, $saveOptions] = $this->parseThumbnailConfig($filenameParts);

        $requestConfiguration->setTransformations($transformations);
        $requestConfiguration->setSaveOptions($saveOptions);

        return $requestConfiguration;
    }

    private function extractPath(string $filename): array
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

        return pathinfo($filename);
    }

    private function extractDirectory(array $path): string
    {
        if (!in_array(strtolower($path['extension']), static::getSupportedExtensions())) {
            throw new InvalidImageException(
                "Filename extension '{$path['extension']}' is not supported"
            );
        }

        $directory = $path['dirname'] === '.' ? '' : "/{$path['dirname']}";
        foreach (explode('/', $directory) as $dirpart) {
            if (strpos($dirpart, '.') !== false) {
                throw new InvalidImageException("Dots are not allowed in directory names");
            }
        }

        return $directory;
    }

    public static function getSupportedExtensions(): array
    {
        return ['jpg', 'jpeg'];
    }

    private function parseThumbnailConfig(array $filenameParts): array
    {
        $filenamePartsCount = count($filenameParts);

        $transformations = new TransformationsCollection();
        $saveOptions = new SaveOptions();

        if ($filenamePartsCount > 1) {
            for ($i = 1; $i < $filenamePartsCount; $i++) {

                $transformation = $this->transformationsFactory->create($filenameParts[$i]);
                if ($transformation !== null) {
                    $transformations->add($transformation);
                    continue;
                }

                $this->saveOptionsConfigurator->configure($saveOptions, $filenameParts[$i]);

            }
        }

        return [$transformations, $saveOptions];
    }
}