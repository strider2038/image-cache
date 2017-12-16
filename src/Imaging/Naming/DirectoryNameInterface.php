<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Naming;

use Strider2038\ImgCache\Core\ModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface DirectoryNameInterface extends ModelInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\NotIdenticalTo("/")
     * @Assert\Regex(
     *     pattern="/^[a-z0-9_\-\.\/]+$/i",
     *     message="Directory name can contain only latin symbols, digits, dots, slashes and '_', '-'"
     * )
     * @Assert\Regex(
     *     pattern="/.*\/$/i",
     *     message="Directory name must end with slash"
     * )
     * @Assert\Regex(
     *     pattern="/\/{2,}/",
     *     match=false,
     *     message="Directory name cannot contain duplicating slashes"
     * )
     * @return string
     */
    public function getValue(): string;

    public function __toString();
}
