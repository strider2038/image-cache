<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Accessor;

use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSourceInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapAccessor implements YandexMapAccessorInterface
{
    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationsFormatterInterface */
    private $formatter;

    /** @var YandexMapSourceInterface */
    private $source;

    public function __construct(
        ModelValidatorInterface $validator,
        ViolationsFormatterInterface $formatter,
        YandexMapSourceInterface $source
    ) {
        $this->validator = $validator;
        $this->formatter = $formatter;
        $this->source = $source;
    }

    public function get(YandexMapParameters $parameters): ImageInterface
    {
        $violations = $this->validator->validate($parameters);
        if ($violations->count()) {
            throw new InvalidRequestValueException(
                'Invalid map parameters: ' . $this->formatter->format($violations)
            );
        }

        $query = new QueryParametersCollection([
            new QueryParameter('l', $parameters->getLayers()->implode()),
            new QueryParameter('ll', sprintf(
                '%s,%s',
                $parameters->getLongitude(),
                $parameters->getLatitude())
            ),
            new QueryParameter('z', $parameters->getZoom()),
            new QueryParameter('size', sprintf(
                '%s,%s',
                $parameters->getWidth(),
                $parameters->getHeight()
            )),
            new QueryParameter('scale', $parameters->getScale())
        ]);

        return $this->source->get($query);
    }
}
