<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Data;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParameters implements EntityInterface, \JsonSerializable
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\Strider2038\ImgCache\Collection\StringList")
     * @Assert\Count(min = 1)
     * @Assert\All({
     *     @Assert\Choice(
     *      choices={"map", "sat", "skl", "trf"},
     *      strict=true
     *     )
     * })
     * @var StringList
     */
    public $layers;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\Range(min = -180, max = 180)
     * @var float
     */
    public $longitude;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\Range(min = -90, max = 90)
     * @var float
     */
    public $latitude;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 0, max = 17)
     * @var int
     */
    public $zoom;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 50, max = 650)
     * @var int
     */
    public $width;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     * @Assert\Range(min = 50, max = 450)
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

    public function __construct()
    {
        $this->layers = new StringList();
    }

    public function getId(): string
    {
        return 'yandex map parameters';
    }

    public function jsonSerialize(): array
    {
        return [
            'layers' => $this->layers->toArray(),
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'zoom' => $this->zoom,
            'width' => $this->width,
            'height' => $this->height,
            'scale' => $this->scale,
        ];
    }
}
