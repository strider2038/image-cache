<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RuntimeLoggerProcessor
{
    /** @var float */
    private $startUpTime;

    public function __construct(float $startUpTime)
    {
        $this->startUpTime = $startUpTime;
    }

    public function __invoke(array $record)
    {
        $runtime = microtime(true) - $this->startUpTime;
        $record['extra']['runtime'] = number_format($runtime * 1000, 3, '.', '');

        return $record;
    }
}
