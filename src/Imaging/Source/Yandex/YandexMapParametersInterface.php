<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Yandex;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\ModelInterface;
use Strider2038\ImgCache\Imaging\Validation\Constraints\ListElementsInListConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface YandexMapParametersInterface extends ModelInterface
{
    /**
     * @ListElementsInListConstraint({"map", "sat", "skl", "trf"})
     * @return StringList
     */
    public function getLayers(): StringList;

    /**
     * @Assert\GreaterThanOrEqual(-180)
     * @Assert\LessThanOrEqual(180)
     * @return float
     */
    public function getLongitude(): float;

    /**
     * @Assert\GreaterThanOrEqual(-180)
     * @Assert\LessThanOrEqual(180)
     * @return float
     */
    public function getLatitude(): float;

    /**
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(17)
     * @return int
     */
    public function getZoom(): int;

    /**
     * @Assert\GreaterThanOrEqual(50)
     * @Assert\LessThanOrEqual(650)
     * @return int
     */
    public function getWidth(): int;

    /**
     * @Assert\GreaterThanOrEqual(50)
     * @Assert\LessThanOrEqual(450)
     * @return int
     */
    public function getHeight(): int;

    /**
     * @Assert\GreaterThanOrEqual(1.0)
     * @Assert\LessThanOrEqual(4.0)
     * @return float
     */
    public function getScale(): float;
}
