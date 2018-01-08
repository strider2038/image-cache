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

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageMimeType extends Constraint
{
    public $message = 'Data mime type is not a valid image mime type';
    public $mimeTypes = [
        'image/jpeg',
        'image/png',
    ];
}
