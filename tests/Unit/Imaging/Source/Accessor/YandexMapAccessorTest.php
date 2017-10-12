<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Accessor;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Source\Accessor\YandexMapAccessor;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParametersInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSourceInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class YandexMapAccessorTest extends TestCase
{
    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationsFormatterInterface */
    private $formatter;

    /** @var YandexMapSourceInterface */
    private $source;

    protected function setUp()
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->formatter = \Phake::mock(ViolationsFormatterInterface::class);
        $this->source = \Phake::mock(YandexMapSourceInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid map parameters: formatted violations
     */
    public function get_givenInvalidParameters_exceptionThrown(): void
    {
        $source = $this->createAccessor();
        $parameters = \Phake::mock(YandexMapParametersInterface::class);
        $violations = $this->givenValidator_validate_returnViolations($parameters);
        $this->givenViolations_count_returns($violations, 1);
        \Phake::when($this->formatter)->format($violations)->thenReturn('formatted violations');

        $source->get($parameters);
    }

    private function createAccessor(): YandexMapAccessor
    {
        return new YandexMapAccessor($this->validator, $this->formatter, $this->source);
    }

    private function givenValidator_validate_returnViolations(
        YandexMapParametersInterface $parameters
    ): ConstraintViolationListInterface {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validate($parameters)->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returns(ConstraintViolationListInterface $violations, int $count): void
    {
        \Phake::when($violations)->count()->thenReturn($count);
    }
}
