<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Routing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Strider2038\ImgCache\Service\Routing\RoutingPathFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class RoutingPathFactoryTest extends TestCase
{
    private const PREFIX = 'prefix';
    private const CONTROLLER_ID = 'controllerId';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /** @test */
    public function createRoutingPathCollection_givenValidRoutingMapInArray_mapWithValidPathsCreated(): void
    {
        $mapInArray = [self::PREFIX => self::CONTROLLER_ID];
        $factory = $this->createRoutingPathFactory();

        $map = $factory->createRoutingPathCollection($mapInArray);

        $this->assertCount(1, $map);
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            RoutingPath::class,
            InvalidConfigurationException::class
        );
        /** @var RoutingPath $firstPath */
        $firstPath = $map->first();
        $this->assertEquals(self::PREFIX, $firstPath->getUrlPrefix());
        $this->assertEquals(self::CONTROLLER_ID, $firstPath->getControllerId());
    }

    private function createRoutingPathFactory(): RoutingPathFactory
    {
        return new RoutingPathFactory($this->validator);
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
