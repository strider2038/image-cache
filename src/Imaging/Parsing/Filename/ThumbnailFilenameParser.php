<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Filename;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailFilenameParser implements ThumbnailFilenameParserInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getParsedFilename(string $filename): ThumbnailFilename
    {
        $path = pathinfo($filename);
        $baseFilename = trim($path['filename'] ?? '');
        $extension = $path['extension'] ?? '';
        $directoryName = $path['dirname'] ?? '';

        $filenameParts = explode('_', $baseFilename);
        $directory = $directoryName === '.' ? '' : (rtrim($directoryName, '/') . '/');
        $sourceFilename = array_shift($filenameParts);

        $fullSourceFilename = sprintf('%s%s.%s', $directory, $sourceFilename, $extension);
        $thumbnailMask = sprintf('%s%s*.%s', $directory, $sourceFilename, $extension);
        $processingConfiguration = implode('_', $filenameParts);

        $thumbnailFilename = new ThumbnailFilename($fullSourceFilename, $thumbnailMask, $processingConfiguration);
        $this->validator->validateWithException($thumbnailFilename, InvalidRequestValueException::class);

        return $thumbnailFilename;
    }
}
