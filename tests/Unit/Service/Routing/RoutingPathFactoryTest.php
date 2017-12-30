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
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Strider2038\ImgCache\Service\Routing\RoutingPathFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RoutingPathFactoryTest extends TestCase
{
    private const PREFIX = 'prefix';
    private const CONTROLLER_ID = 'controllerId';

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->violationFormatter = \Phake::mock(ViolationFormatterInterface::class);
    }

    /** @test */
    public function createRoutingPathCollection_givenValidRoutingMapInArray_mapWithValidPathsCreated(): void
    {
        $mapInArray = [self::PREFIX => self::CONTROLLER_ID];
        $factory = $this->createRoutingPathFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);

        $map = $factory->createRoutingPathCollection($mapInArray);

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
    public function createRoutingPathCollection_givenInvalidRoutingMapInArray_exceptionThrown(): void
    {
        $mapInArray = [self::PREFIX => self::CONTROLLER_ID];
        $factory = $this->createRoutingPathFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 1);

        $factory->createRoutingPathCollection($mapInArray);
    }

    private function createRoutingPathFactory(): RoutingPathFactory
    {
        return new RoutingPathFactory($this->validator, $this->violationFormatter);
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
