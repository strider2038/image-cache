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

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSourceInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapExtractor implements ImageExtractorInterface
{
    /** @var YandexMapParametersParserInterface */
    private $parser;

    /** @var YandexMapSourceInterface */
    private $source;

    public function __construct(YandexMapParametersParserInterface $parser, YandexMapSourceInterface $source)
    {
        $this->parser = $parser;
        $this->source = $source;
    }

    public function extract(string $key): ? ImageInterface
    {
        $parameters = $this->parser->parse($key);
        return $this->source->get($parameters);
    }
}
