<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Validation\Constraints;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Validation\Constraints\ListElementsInListConstraint;

class ListElementsInListConstraintTest extends TestCase
{
    /**
     * @test
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage constraint requires the "value" option to be set
     */
    public function construct_optionsAreEmpty_exceptionThrown(): void
    {
        new ListElementsInListConstraint();
    }

    /** @test */
    public function construct_optionsHasValue_valuesAreCreatedAsStringList(): void
    {
        $constraint = new ListElementsInListConstraint([
            'value' => ['value1', 'value2']
        ]);

        $this->assertInstanceOf(StringList::class, $constraint->values);
        $this->assertEquals(['value1', 'value2'], $constraint->values->toArray());
    }
}
