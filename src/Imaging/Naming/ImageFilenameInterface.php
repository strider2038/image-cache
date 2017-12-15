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
interface ImageFilenameInterface extends ModelInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^\/.*$/i",
     *     match=false,
     *     message="Filename cannot start from slash"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9_=\+\-\.\/]+$/i",
     *     message="Filename can contain only latin symbols, digits, dots, slashes and '_', '+', '-', '='"
     * )
     * @Assert\Regex(
     *     pattern="/\/{2,}|\.{2,}/",
     *     match=false,
     *     message="Filename cannot contain duplicating slashes or dots"
     * )
     * @Assert\Regex(
     *     pattern="/.*\.(jpg|jpeg|png)$/",
     *     message="Only 'jpg', 'jpeg' and 'png' lowercase extensions allowed"
     * )
     * @return string
     */
    public function getValue(): string;
}
