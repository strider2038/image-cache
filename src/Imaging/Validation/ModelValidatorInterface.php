<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Validation;

use Strider2038\ImgCache\Core\ModelInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ModelValidatorInterface
{
    public function validateModel(ModelInterface $model): ConstraintViolationListInterface;
}
