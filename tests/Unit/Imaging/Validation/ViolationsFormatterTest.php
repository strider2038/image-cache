<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Validation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\ViolationFormatter;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ViolationsFormatterTest extends TestCase
{
    /** @test */
    public function format_givenViolations_stringIsReturned(): void
    {
        $violations = new ConstraintViolationList([
            $this->createViolation('message1', 'property1'),
            $this->createViolation('message2', 'property2'),
        ]);
        $formatter = new ViolationFormatter();

        $report = $formatter->formatViolations($violations);

        $this->assertEquals('property1: message1; property2: message2', $report);
    }

    private function createViolation(string $message, string $property): ConstraintViolation
    {
        return new ConstraintViolation(
            $message,
            '',
            [],
            '',
            $property,
            ''
        );
    }
}
