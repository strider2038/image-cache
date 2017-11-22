<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\YandexMapAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapExtractor implements ImageExtractorInterface
{
    /** @var YandexMapParametersParserInterface */
    private $parser;

    /** @var YandexMapAccessorInterface */
    private $accessor;

    public function __construct(YandexMapParametersParserInterface $parser, YandexMapAccessorInterface $accessor)
    {
        $this->parser = $parser;
        $this->accessor = $accessor;
    }

    public function extractImage(string $key): Image
    {
        $parameters = $this->parser->parse($key);

        return $this->accessor->get($parameters);
    }
}
