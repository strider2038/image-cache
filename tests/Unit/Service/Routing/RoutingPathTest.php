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
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Symfony\Component\Validator\ConstraintViolationInterface;

class RoutingPathTest extends TestCase
{
    /** @var ModelValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new ModelValidator();
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
