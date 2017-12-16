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

use Doctrine\Common\Annotations\AnnotationRegistry;
use Strider2038\ImgCache\Core\ModelInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ModelValidator implements ModelValidatorInterface
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

    public function validateModel(ModelInterface $model): ConstraintViolationListInterface
    {
        return $this->validator->validate($model);
    }
}
