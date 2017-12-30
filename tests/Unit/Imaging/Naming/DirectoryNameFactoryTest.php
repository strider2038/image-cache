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
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactory;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\ViolationFormatterInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class DirectoryNameFactoryTest extends TestCase
{
    use FileOperationsTrait;

    private const DIRECTORY_NAME = 'directory_name';

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->violationFormatter = \Phake::mock(ViolationFormatterInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    /**
     * @test
     * @dataProvider directoryNameProvider
     * @param string $name
     * @param string $expectedFilenameValue
     * @throws \Strider2038\ImgCache\Exception\InvalidConfigurationException
     */
    public function createDirectoryName_givenName_DirectoryNameCreatedAndReturned(
        string $name,
        string $expectedFilenameValue
    ): void {
        $factory = $this->createDirectoryNameFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);

        $directoryName = $factory->createDirectoryName($name);

        $this->assertInstanceOf(DirectoryName::class, $directoryName);
        $this->assertEquals($expectedFilenameValue, $directoryName->getValue());
        $this->assetValidator_validate_isCalledOnceWithAnyParameter();
    }

    public function directoryNameProvider(): array
    {
        return [
            [self::DIRECTORY_NAME, self::DIRECTORY_NAME . '/'],
            [self::DIRECTORY_NAME . '/', self::DIRECTORY_NAME . '/'],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Given invalid directory name
     */
    public function createDirectoryName_givenInvalidName_invalidConfigurationExceptionThrown(): void
    {
        $factory = $this->createDirectoryNameFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 1);

        $factory->createDirectoryName(self::DIRECTORY_NAME);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function createDirectoryName_givenNotExistingNameAndCheckExistenceIsTrue_invalidConfigurationExceptionThrown(): void
    {
        $factory = $this->createDirectoryNameFactory();
        $violations = $this->givenValidator_validate_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::DIRECTORY_NAME, false);

        $factory->createDirectoryName(self::DIRECTORY_NAME, true);
    }

    private function assetValidator_validate_isCalledOnceWithAnyParameter(): void
    {
        \Phake::verify($this->validator, \Phake::times(1))->validate(\Phake::anyParameters());
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

    private function createDirectoryNameFactory(): DirectoryNameFactory
    {
        return new DirectoryNameFactory(
            $this->validator,
            $this->violationFormatter,
            $this->fileOperations
        );
    }
}
