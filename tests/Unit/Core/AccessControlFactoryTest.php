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
use Strider2038\ImgCache\Core\AccessControlFactory;
use Strider2038\ImgCache\Core\BearerWriteAccessControl;
use Strider2038\ImgCache\Core\ReadOnlyAccessControl;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class AccessControlFactoryTest extends TestCase
{
    use LoggerTrait;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function createAccessControlByToken_givenTokenIsEmpty_readOnlyAccessControlCreatedAndReturned(): void
    {
        $factory = $this->createAccessControlFactory();

        $accessControl = $factory->createAccessControlByToken('');

        $this->assertInstanceOf(ReadOnlyAccessControl::class, $accessControl);
        $this->assertLogger_debug_isCalledTimes($this->logger, 1);
        $this->assertLogger_warning_isNeverCalled($this->logger);
    }

    /** @test */
    public function createAccessControlByToken_givenTokenIsLong_bearerWriteAccessControlCreatedAndReturned(): void
    {
        $factory = $this->createAccessControlFactory();

        $accessControl = $factory->createAccessControlByToken('token_123456789123456789');

        $this->assertInstanceOf(BearerWriteAccessControl::class, $accessControl);
        $this->assertLogger_debug_isCalledTimes($this->logger, 1);
        $this->assertLogger_warning_isNeverCalled($this->logger);
    }

    /** @test */
    public function createAccessControlByToken_givenTokenIsShort_bearerWriteAccessControlCreatedAndReturnedAndWarningSendToLogger(): void
    {
        $factory = $this->createAccessControlFactory();

        $accessControl = $factory->createAccessControlByToken('token');

        $this->assertInstanceOf(BearerWriteAccessControl::class, $accessControl);
        $this->assertLogger_debug_isCalledTimes($this->logger, 1);
        $this->assertLogger_warning_isCalledOnce($this->logger);
    }

    private function createAccessControlFactory(): AccessControlFactory
    {
        $factory = new AccessControlFactory();
        $factory->setLogger($this->logger);

        return $factory;
    }
}
