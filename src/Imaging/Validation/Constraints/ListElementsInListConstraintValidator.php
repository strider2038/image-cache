<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Validation\Constraints;

use Strider2038\ImgCache\Collection\StringList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ListElementsInListConstraintValidator extends ConstraintValidator
{
    /**
     * @param StringList $value
     * @param Constraint $constraint
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof StringList) {
            throw new UnexpectedTypeException($value, StringList::class);
        }

        foreach ($value as $element) {
            if (!$constraint->values->contains($element)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ list }}', $value->implode(', '))
                    ->setParameter('{{ values }}', $constraint->values->implode(', '))
                    ->addViolation();
                return;
            }
        }
    }
}
