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
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;
use Strider2038\ImgCache\Service\Routing\RoutingMapFactory;
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RoutingMapFactoryTest extends TestCase
{
    private const PREFIX = 'prefix';
    private const CONTROLLER_ID = 'controllerId';

    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationsFormatterInterface */
    private $violationsFormatter;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->violationsFormatter = \Phake::mock(ViolationsFormatterInterface::class);
    }

    /** @test */
    public function createRoutingMap_givenValidRoutingMapInArray_mapWithValidPathsCreated(): void
    {
        $mapInArray = [self::PREFIX => self::CONTROLLER_ID];
        $factory = $this->createRoutingMapFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);

        $map = $factory->createRoutingMap($mapInArray);

        $this->assertCount(1, $map);
        /** @var RoutingPath $firstPath */
        $firstPath = $map->first();
        $this->assertEquals(self::PREFIX, $firstPath->getUrlPrefix());
        $this->assertEquals(self::CONTROLLER_ID, $firstPath->getControllerId());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Invalid routing map
     */
    public function createRoutingMap_givenInvalidRoutingMapInArray_exceptionThrown(): void
    {
        $mapInArray = [self::PREFIX => self::CONTROLLER_ID];
        $factory = $this->createRoutingMapFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 1);

        $factory->createRoutingMap($mapInArray);
    }

    private function createRoutingMapFactory(): RoutingMapFactory
    {
        return new RoutingMapFactory($this->validator, $this->violationsFormatter);
    }

    private function givenValidator_validate_returnViolations(): ConstraintViolationListInterface
    {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validate(\Phake::anyParameters())->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returnsCount(ConstraintViolationListInterface $violations, int $count): void
    {
        \Phake::when($violations)->count()->thenReturn($count);
    }
}
