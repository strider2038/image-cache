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
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class DirectoryNameTest extends TestCase
{
    private const DIRECTORY_NAME_ID = 'directory name';

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
        $directoryName = new DirectoryName('');

        $id = $directoryName->getId();

        $this->assertEquals(self::DIRECTORY_NAME_ID, $id);
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

        $violations = $this->validator->validate($directoryName);

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
