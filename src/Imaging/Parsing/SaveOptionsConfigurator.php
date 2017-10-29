<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SaveOptionsConfigurator implements SaveOptionsConfiguratorInterface
{
    public function configure(SaveOptions $saveOptions, string $configuration): void
    {
        if (strlen($configuration) <= 0 || $configuration[0] !== 'q') {
            return;
        }

        $value = substr($configuration, 1);

        if (!preg_match('/^\d+$/', $value)) {
            throw new InvalidRequestValueException('Invalid configuration for quality transformation');
        }

        $saveOptions->setQuality((int) $value);
    }
}
