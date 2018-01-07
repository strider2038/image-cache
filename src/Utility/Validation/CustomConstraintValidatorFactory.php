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

use Strider2038\ImgCache\Utility\MetadataReaderInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CustomConstraintValidatorFactory extends ConstraintValidatorFactory
{
    public function __construct(MetadataReaderInterface $metadataReader)
    {
        parent::__construct();
        $this->validators[ImageMimeTypeValidator::class] = new ImageMimeTypeValidator($metadataReader);
    }
}
