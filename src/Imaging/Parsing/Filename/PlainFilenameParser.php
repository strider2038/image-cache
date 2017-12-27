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
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class PlainFilenameParser implements PlainFilenameParserInterface
{
    /** @var KeyValidatorInterface */
    private $keyValidator;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    public function __construct(
        KeyValidatorInterface $keyValidator,
        ImageValidatorInterface $imageValidator
    ) {
        $this->keyValidator = $keyValidator;
        $this->imageValidator = $imageValidator;
    }

    public function getParsedFilename(string $filename): PlainFilename
    {
        if (!$this->keyValidator->isValidPublicFilename($filename)) {
            throw new InvalidRequestValueException("Invalid filename '{$filename}' in request");
        }
        if (!$this->imageValidator->hasValidImageExtension($filename)) {
            throw new InvalidRequestValueException("Unsupported image extension for '{$filename}'");
        }

        return new PlainFilename($filename);
    }
}
