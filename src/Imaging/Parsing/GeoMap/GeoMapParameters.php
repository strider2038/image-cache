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
     * @Assert\Choice(
     *     choices={"roadmap", "satellite", "hybrid", "terrain"},
     *     strict=true
     * )
     * @var string
     */
    public $type;

    /**
     * @Assert\Range(min = -90, max = 90)
     * @var float
     */
    public $latitude;

    /**
     * @Assert\Range(min = -180, max = 180)
     * @var float
     */
    public $longitude;

    /**
     * @Assert\Range(min = 1, max = 20)
     * @var int
     */
    public $zoom;

    /**
     * @Assert\Range(min = 50, max = 640)
     * @var int
     */
    public $width;

    /**
     * @Assert\Range(min = 50, max = 640)
     * @var int
     */
    public $height;

    /**
     * @Assert\Range(min = 1.0, max = 4.0)
     * @var float
     */
    public $scale;

    public function getId(): string
    {
        return 'geographical map parameters';
    }
}
