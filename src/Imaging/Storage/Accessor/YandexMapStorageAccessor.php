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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriverInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapStorageAccessor implements YandexMapStorageAccessorInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    /** @var YandexMapStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EntityValidatorInterface $validator,
        ViolationFormatterInterface $violationFormatter,
        YandexMapStorageDriverInterface $storageDriver,
        ImageFactoryInterface $imageFactory
    ) {
        $this->validator = $validator;
        $this->violationFormatter = $violationFormatter;
        $this->storageDriver = $storageDriver;
        $this->imageFactory = $imageFactory;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getImage(YandexMapParameters $parameters): Image
    {
        $this->logger->info(
            sprintf(
                'Requesting yandex static map with parameters: %s.',
                json_encode($parameters)
            )
        );

        $violations = $this->validator->validate($parameters);

        if ($violations->count()) {
            throw new InvalidRequestValueException(
                sprintf('Invalid map parameters: %s.', $this->violationFormatter->formatViolations($violations))
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

        $stream = $this->storageDriver->getMapContents($query);

        return $this->imageFactory->createFromStream($stream);
    }
}
