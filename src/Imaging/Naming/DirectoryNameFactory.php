<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Naming;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DirectoryNameFactory implements DirectoryNameFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function __construct(
        EntityValidatorInterface $validator,
        FileOperationsInterface $fileOperations
    ) {
        $this->validator = $validator;
        $this->fileOperations = $fileOperations;
    }

    public function createDirectoryName(string $directoryName, bool $checkExistence = false): DirectoryNameInterface
    {
        $name = new DirectoryName(rtrim($directoryName, '/') . '/');
        $this->validator->validateWithException($name, InvalidConfigurationException::class);

        if ($checkExistence && !$this->fileOperations->isDirectory($directoryName)) {
            throw new InvalidConfigurationException(
                sprintf('Directory "%s" does not exist.', $directoryName)
            );
        }

        return $name;
    }
}
