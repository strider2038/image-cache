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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface YandexMapParametersInterface extends ModelInterface
{
    public function getLayers(): StringList;
    public function getLongitude(): float;
    public function getLatitude(): float;
    public function getZoom(): int;
    public function getWidth(): int;
    public function getHeight(): int;
    public function getScale(): float;
}
