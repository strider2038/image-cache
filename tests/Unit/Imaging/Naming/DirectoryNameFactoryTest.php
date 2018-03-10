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
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactory;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class DirectoryNameFactoryTest extends TestCase
{
    use FileOperationsTrait;

    private const DIRECTORY_NAME = 'directory_name';

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    /**
     * @test
     * @dataProvider directoryNameProvider
     * @param string $name
     * @param string $expectedFilenameValue
     */
    public function createDirectoryName_givenName_DirectoryNameCreatedAndReturned(
        string $name,
        string $expectedFilenameValue
    ): void {
        $factory = $this->createDirectoryNameFactory();

        $directoryName = $factory->createDirectoryName($name);

        $this->assertInstanceOf(DirectoryName::class, $directoryName);
        $this->assertEquals($expectedFilenameValue, $directoryName->getValue());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            DirectoryName::class,
            InvalidConfigurationException::class
        );
    }

    public function directoryNameProvider(): array
    {
        return [
            [self::DIRECTORY_NAME, self::DIRECTORY_NAME . '/'],
            [self::DIRECTORY_NAME . '/', self::DIRECTORY_NAME . '/'],
        ];
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }

    private function createDirectoryNameFactory(): DirectoryNameFactory
    {
        return new DirectoryNameFactory($this->validator);
    }
}
