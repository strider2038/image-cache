<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Filename;

use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailFilename implements EntityInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\NotIdenticalTo("/")
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9_\-\.\/]+$/",
     *     message="Filename must contain only latin symbols, digits, snakes '_', dots '.' and slashes '/'"
     * )
     * @Assert\Regex(
     *     pattern="/\/{2,}|\.{2,}/",
     *     match=false,
     *     message="Filename cannot contain duplicating slashes or dots"
     * )
     * @Assert\Regex(
     *     pattern="/^.*\..*\/(.*)/",
     *     match=false,
     *     message="Dots are not allowed in directory names"
     * )
     * @Assert\Regex(
     *     pattern="/.*\.(jpg|jpeg|png)$/",
     *     message="Only 'jpg', 'jpeg' and 'png' lowercase extensions allowed"
     * )
     * @Assert\Regex(
     *     pattern="/.*_\.[a-z]{3,4}$/",
     *     match=false,
     *     message="Empty processing configuration is not allowed"
     * )
     * @var string
     */
    private $value;

    /** @var string */
    private $mask;

    /** @var string */
    private $processingConfiguration;

    public function __construct(
        string $value,
        string $mask,
        string $processingConfiguration
    ) {
        $this->value = $value;
        $this->mask = $mask;
        $this->processingConfiguration = $processingConfiguration;
    }

    public function getId(): string
    {
        return 'thumbnail filename';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function getProcessingConfiguration(): string
    {
        return $this->processingConfiguration;
    }

    public function hasProcessingConfiguration(): bool
    {
        return $this->processingConfiguration !== '';
    }
}
