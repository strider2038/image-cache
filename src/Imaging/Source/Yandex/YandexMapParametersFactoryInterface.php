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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface YandexMapParametersFactoryInterface
{
    public function create(): YandexMapParametersInterface;
}
