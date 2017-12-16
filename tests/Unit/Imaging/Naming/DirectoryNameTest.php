<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Naming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;

class DirectoryNameTest extends TestCase
{
    /** @var ModelValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new ModelValidator();
    }

    /**
     * @test
     * @dataProvider valueProvider
     * @param string $value
     * @param int $violationsCount
     */
    public function validate_givenImageFilename_violationsReturned(string $value, int $violationsCount): void
    {
        $directoryName = new DirectoryName($value);

        $violations = $this->validator->validateModel($directoryName);

        $this->assertCount($violationsCount, $violations);
    }

    public function valueProvider(): array
    {
        return [
            ['', 1],
            ['/', 1],
            ['/directory//name/', 1],
            ['/$directory/', 1],
            ['directory', 1],
            ['/Directory_Name/sub.a-b/', 0],
        ];
    }
}
