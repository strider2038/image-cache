<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Thumbnail;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParser implements ThumbnailKeyParserInterface
{
    /** @var KeyValidatorInterface */
    private $keyValidator;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    public function __construct(
        KeyValidatorInterface $keyValidator,
        ImageValidatorInterface $imageValidator,
        ProcessingConfigurationParserInterface $configurationParser
    ) {
        $this->keyValidator = $keyValidator;
        $this->imageValidator = $imageValidator;
        $this->processingConfigurationParser = $configurationParser;
    }

    public function parse(string $key): ThumbnailKey
    {
        if (!$this->keyValidator->isValidPublicFilename($key)) {
            throw new InvalidRequestValueException(sprintf('Invalid filename "%s" in request', $key));
        }
        if (!$this->imageValidator->hasValidImageExtension($key)) {
            throw new InvalidRequestValueException(sprintf('Unsupported image extension for "%s"', $key));
        }

        $path = pathinfo($key);
        $filename = trim($path['filename'] ?? '');
        $filenameLength = strlen($filename);
        $extension = $path['extension'] ?? '';
        $directoryName = $path['dirname'] ?? '';
        if ($filenameLength <= 0 || $filename[$filenameLength - 1] === '_') {
            throw new InvalidRequestValueException(sprintf('Invalid filename "%s" in request', $key));
        }

        $filenameParts = explode('_', $filename);
        $directory = $directoryName === '.' ? '' : (rtrim($directoryName, '/') . '/');
        $baseFilename = array_shift($filenameParts);
        $sourceFilename = sprintf('%s%s.%s', $directory, $baseFilename, $extension);
        $thumbnailMask = sprintf('%s%s*.%s', $directory, $baseFilename, $extension);

        $processingConfigurationString = implode('_', $filenameParts);
        $processingConfiguration = $this->processingConfigurationParser->parseConfiguration($processingConfigurationString);

        return new ThumbnailKey($sourceFilename, $thumbnailMask, $processingConfiguration);
    }
}
