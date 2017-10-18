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
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * @Annotation
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ListElementsInListConstraint extends Constraint
{
    public $message = 'The list "{{ list }}" contains an illegal values: it can only contain {{ values }}.';
    public $values;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (null === $options) {
            $options = [];
        }

        if (is_array($options) && !isset($options['value'])) {
            throw new ConstraintDefinitionException(sprintf(
                'The %s constraint requires the "value" option to be set.',
                get_class($this)
            ));
        }

        $this->values = new StringList($options['value']);
        unset($options['value']);

        parent::__construct($options);
    }
}
