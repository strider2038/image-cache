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

use Doctrine\Common\Annotations\AnnotationRegistry;
use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class EntityValidator implements EntityValidatorInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    public function __construct(ViolationFormatterInterface $violationFormatter)
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $this->violationFormatter = $violationFormatter;
    }

    public function validate(EntityInterface $entity): ConstraintViolationListInterface
    {
        return $this->validator->validate($entity);
    }

    public function validateWithException(EntityInterface $entity, string $exceptionClass): void
    {
        $violations = $this->validate($entity);

        if ($violations->count() > 0) {
            throw new $exceptionClass(
                sprintf(
                    'Given invalid %s: %s',
                    $entity->getId(),
                    $this->violationFormatter->formatViolations($violations)
                )
            );
        }
    }
}
