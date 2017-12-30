<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface EntityValidatorInterface
{
    public function validate(EntityInterface $entity): ConstraintViolationListInterface;
    public function validateWithException(EntityInterface $entity, string $exceptionClass): void;
}
