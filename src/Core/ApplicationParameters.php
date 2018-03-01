<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationParameters
{
    /** @var string */
    private $rootDirectory;

    /**
     * Array with global request and server parameters
     * (by default set from global variable $_SERVER)
     * @var array
     */
    private $serverConfiguration;

    /** @var float */
    private $startUpTime;

    public function __construct(string $rootDirectory, array $serverConfiguration = null)
    {
        $this->rootDirectory = $rootDirectory;
        $this->serverConfiguration = $serverConfiguration ?? $_SERVER;
        $this->startUpTime = microtime(true);
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    public function getServerConfiguration(): array
    {
        return $this->serverConfiguration;
    }

    public function getStartUpTime(): float
    {
        return $this->startUpTime;
    }
}
