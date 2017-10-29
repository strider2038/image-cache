<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Source;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceKeyParser implements SourceKeyParserInterface
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

    public function parse(string $key): SourceKey
    {
        if (!$this->keyValidator->isValidPublicFilename($key)) {
            throw new InvalidRequestValueException("Invalid filename '{$key}' in request");
        }
        if (!$this->imageValidator->hasValidImageExtension($key)) {
            throw new InvalidRequestValueException("Unsupported image extension for '{$key}'");
        }

        return new SourceKey($key);
    }
}
