<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility\Validation;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageMimeTypeValidator extends ConstraintValidator
{
    /** @var MetadataReaderInterface */
    private $metadataReader;

    public function __construct(MetadataReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var ImageMimeType $constraint */
        if (!$value instanceof StreamInterface) {
            throw new UnexpectedTypeException($value, StreamInterface::class);
        }

        $mimeType = $this->metadataReader->getContentTypeFromStream($value);

        if (!\in_array($mimeType, $constraint->mimeTypes, true)) {
            $violationBuilder = $this->context->buildViolation($constraint->message);
            $violationBuilder->addViolation();
        }
    }
}
