<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Core\Service\ApplicationServiceInterface;
use Strider2038\ImgCache\Core\Service\SequentialServiceRunner;
use Strider2038\ImgCache\Tests\Support\DummyClass;

class SequentialServiceRunnerTest extends TestCase
{
    private const SERVICE_RUNNING_SEQUENCE_ID = 'service_running_sequence';
    private const APPLICATION_SERVICE_ID = 'application_service_id';

    /** @var ContainerInterface */
    private $container;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerInterface::class);
    }

    /** @test */
    public function runServices_givenContainerWithValidServiceSequence_servicesRunningSequentially(): void
    {
        $runner = new SequentialServiceRunner();
        $this->givenContainer_get_withId_returnsObject(self::SERVICE_RUNNING_SEQUENCE_ID, [self::APPLICATION_SERVICE_ID]);
        $service = \Phake::mock(ApplicationServiceInterface::class);
        $this->givenContainer_get_withId_returnsObject(self::APPLICATION_SERVICE_ID, $service);

        $runner->runServices($this->container);

        $this->assertContainer_get_isCalledOnceWithId(self::SERVICE_RUNNING_SEQUENCE_ID);
        $this->assertContainer_get_isCalledOnceWithId(self::APPLICATION_SERVICE_ID);
        $this->assertApplicationService_run_isCalledOnce($service);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Given service .* must implement/
     */
    public function runServices_givenContainerWithInvalidService_exceptionThrown(): void
    {
        $runner = new SequentialServiceRunner();
        $this->givenContainer_get_withId_returnsObject(self::SERVICE_RUNNING_SEQUENCE_ID, [self::APPLICATION_SERVICE_ID]);
        $this->givenContainer_get_withId_returnsObject(self::APPLICATION_SERVICE_ID, new DummyClass());

        $runner->runServices($this->container);
    }

    private function assertContainer_get_isCalledOnceWithId($id): void
    {
        \Phake::verify($this->container)->get($id);
    }

    private function givenContainer_get_withId_returnsObject(string $id, $object): void
    {
        \Phake::when($this->container)->get($id)->thenReturn($object);
    }

    private function assertApplicationService_run_isCalledOnce(ApplicationServiceInterface $service): void
    {
        \Phake::verify($service, \Phake::times(1))->run();
    }
}
