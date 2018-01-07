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
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;
use Symfony\Component\Validator\ConstraintViolationInterface;

class RoutingPathTest extends TestCase
{
    private const ROUTING_PATH_ID = 'routing path';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $routingPath = new RoutingPath('', '');

        $id = $routingPath->getId();

        $this->assertEquals(self::ROUTING_PATH_ID, $id);
    }

    /**
     * @test
     * @dataProvider urlPrefixAndControllerIdProvider
     * @param string $urlPrefix
     * @param string $controllerId
     * @param string $invalidParameterName
     */
    public function validate_givenUrlPrefixAndControllerId_expectedParameterIsViolated(
        string $urlPrefix,
        string $controllerId,
        string $invalidParameterName
    ): void {
        $path = new RoutingPath($urlPrefix, $controllerId);

        $violations = $this->validator->validate($path);

        $this->assertGreaterThan(0, $violations->count());
        foreach ($violations as $violation) {
            /** @var ConstraintViolationInterface $violation */
            $this->assertEquals($invalidParameterName, $violation->getPropertyPath());
        }
    }

    public function urlPrefixAndControllerIdProvider(): array
    {
        return [
            ['', 'controllerId', 'urlPrefix'],
            ['/', 'controllerId', 'urlPrefix'],
            ['/no_slash_at_end/', 'controllerId', 'urlPrefix'],
            ['/кириллица', 'controllerId', 'urlPrefix'],
            ['/ ', 'controllerId', 'urlPrefix'],
            ['/  ', 'controllerId', 'urlPrefix'],
            ['/i ', 'controllerId', 'urlPrefix'],
            ['/i j', 'controllerId', 'urlPrefix'],
            ['/i//j', 'controllerId', 'urlPrefix'],
            ['/prefix', '', 'controllerId'],
            ['/prefix', '/', 'controllerId'],
            ['/prefix', '', 'controllerId'],
            ['/prefix', ' ', 'controllerId'],
            ['/prefix', 0, 'controllerId'],
        ];
    }
}
