<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration\ImageSource;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class FilesystemImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory';
    private const STORAGE_DIRECTORY = '/storage_directory/';
    private const PROCESSOR_TYPE = 'copy';
    private const IMAGE_STORAGE_SERVICE_ID = 'filesystem_storage';
    private const ENTITY_ID = 'filesystem image source';

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
    public function construct_givenParameters_parametersSetAndAccessible(): void
    {
        $source = new FilesystemImageSource(
            self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            self::PROCESSOR_TYPE
        );

        $this->assertEquals(self::CACHE_DIRECTORY . '/', $source->getCacheDirectory());
        $this->assertEquals(self::STORAGE_DIRECTORY, $source->getStorageDirectory());
        $this->assertEquals(self::PROCESSOR_TYPE, $source->getProcessorType());
        $this->assertEquals(self::IMAGE_STORAGE_SERVICE_ID, $source->getImageStorageServiceId());
        $this->assertEquals(self::ENTITY_ID, $source->getId());
    }

    /**
     * @test
     * @param string $cacheDirectory
     * @param string $storageDirectory
     * @param string $processorType
     * @param int $violationsCount
     * @dataProvider imageSourceParametersProvider
     */
    public function validate_givenParameters_violationsReturned(
        string $cacheDirectory,
        string $storageDirectory,
        string $processorType,
        int $violationsCount
    ): void {
        $imageSource = new FilesystemImageSource(
            $cacheDirectory,
            $storageDirectory,
            $processorType
        );

        $violations = $this->validator->validate($imageSource);

        $this->assertCount($violationsCount, $violations);
    }

    public function imageSourceParametersProvider(): array
    {
        return [
            [self::CACHE_DIRECTORY, self::STORAGE_DIRECTORY, self::PROCESSOR_TYPE, 0],
            [self::CACHE_DIRECTORY, self::STORAGE_DIRECTORY, 'thumbnail', 0],
            ['', '', '', 3],
            ['invalid', self::STORAGE_DIRECTORY, self::PROCESSOR_TYPE, 1],
            [self::CACHE_DIRECTORY, 'invalid', self::PROCESSOR_TYPE, 1],
        ];
    }
}
