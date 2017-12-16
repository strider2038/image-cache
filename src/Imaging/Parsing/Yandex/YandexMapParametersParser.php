<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Yandex;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParametersParser implements YandexMapParametersParserInterface
{
    private const PARAMETERS_DELIMITER = '_';
    private const KEY_VALUE_DELIMITER = '=';

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var ValueConfiguratorFactoryInterface */
    private $valueConfiguratorFactory;

    /** @var YandexMapParametersFactoryInterface */
    private $parametersFactory;

    public function __construct(
        ImageValidatorInterface $imageValidator,
        ValueConfiguratorFactoryInterface $valueConfiguratorFactory,
        YandexMapParametersFactoryInterface $parametersFactory
    ) {
        $this->imageValidator = $imageValidator;
        $this->valueConfiguratorFactory = $valueConfiguratorFactory;
        $this->parametersFactory = $parametersFactory;
    }

    public function parse(string $key): YandexMapParameters
    {
        if (!$this->imageValidator->hasValidImageExtension($key)) {
            throw new InvalidRequestValueException(sprintf('Unsupported image extension for filename "%s".', $key));
        }

        $parameters = $this->parametersFactory->createYandexMapParameters();

        $filename = pathinfo($key, PATHINFO_FILENAME);
        $rawParameters = explode(self::PARAMETERS_DELIMITER, $filename);
        foreach ($rawParameters as $parameter) {
            $parsedParameter = explode(self::KEY_VALUE_DELIMITER, $parameter);
            $configurator = $this->valueConfiguratorFactory->create($parsedParameter[0]);
            $configurator->configure($parsedParameter[1] ?? '', $parameters);
        }

        return $parameters;
    }
}
