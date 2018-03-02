<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Service;

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SequentialServiceRunner implements ServiceRunnerInterface
{
    private const SERVICE_RUNNING_SEQUENCE_ID = 'service_running_sequence';

    public function runServices(ContainerInterface $container): void
    {
        $serviceIds = $container->get(self::SERVICE_RUNNING_SEQUENCE_ID);

        foreach ($serviceIds as $serviceId) {
            /** @var ApplicationServiceInterface $service */
            $service = $container->get($serviceId);
            $this->validateApplicationService($service);
            $service->run();
        }
    }

    private function validateApplicationService($service): void
    {
        if (!$service instanceof ApplicationServiceInterface) {
            throw new ApplicationException(
                sprintf(
                    'Given service "%s" must implement %s',
                    \get_class($service),
                    ApplicationServiceInterface::class
                )
            );
        }
    }
}
