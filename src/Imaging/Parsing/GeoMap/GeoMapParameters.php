<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\GeoMap;

use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapParameters implements EntityInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Choice(
     *     choices={"roadmap", "satellite", "hybrid", "terrain"},
     *     strict=true
     * )
     * @var string
     */
    public $type;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\Range(min = -90, max = 90)
     * @var float
     */
    public $latitude;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\Range(min = -180, max = 180)
     * @var float
     */
    public $longitude;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 1, max = 20)
     * @var int
     */
    public $zoom;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 50, max = 640)
     * @var int
     */
    public $width;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 50, max = 640)
     * @var int
     */
    public $height;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\Range(min = 1.0, max = 4.0)
     * @var float
     */
    public $scale;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Choice(
     *     choices={"jpg", "jpeg", "png"},
     *     strict=true
     * )
     * @var string
     */
    public $imageFormat;

    public function getId(): string
    {
        return 'geographical map parameters';
    }
}
