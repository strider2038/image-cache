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

    public function __construct()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }

    public function validate(EntityInterface $entity): ConstraintViolationListInterface
    {
        return $this->validator->validate($entity);
    }
}
