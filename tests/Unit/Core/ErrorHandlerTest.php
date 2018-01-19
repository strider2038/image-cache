<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\ErrorHandler;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class ErrorHandlerTest extends TestCase
{
    use LoggerTrait;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function register_noParameters_shutdownFunctionRegisteredAndLoggerDebugCalled(): void
    {
        $handler = new ErrorHandler($this->logger);

        $handler->register();

        $this->assertLogger_debug_isCalledTimes($this->logger, 1);
    }

    /** @test */
    public function onShutdown_givenLoggerAndError_loggerCriticalCalled(): void
    {
        ErrorHandler::onShutdown($this->logger, ['message' => 'error']);

        $this->assertLogger_critical_isCalledOnce($this->logger, 'Message: error');
    }
}
