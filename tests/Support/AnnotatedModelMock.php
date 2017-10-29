<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use Strider2038\ImgCache\Core\ModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class AnnotatedModelMock implements ModelInterface
{
    /** @var string */
    private $property = '';

    /**
     * @Assert\NotBlank()
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }
}
