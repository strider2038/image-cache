<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Accessor;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriverInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapStorageAccessor implements YandexMapStorageAccessorInterface
{
    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationsFormatterInterface */
    private $formatter;

    /** @var YandexMapStorageDriverInterface */
    private $storageDriver;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ModelValidatorInterface $validator,
        ViolationsFormatterInterface $formatter,
        YandexMapStorageDriverInterface $storageDriver
    ) {
        $this->validator = $validator;
        $this->formatter = $formatter;
        $this->storageDriver = $storageDriver;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getImage(YandexMapParameters $parameters): Image
    {
        $this->logger->info(sprintf('Requesting yandex static map with parameters: %s', json_encode($parameters)));

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

        return $this->storageDriver->get($query);
    }
}
