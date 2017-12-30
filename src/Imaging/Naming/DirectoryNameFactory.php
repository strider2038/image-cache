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
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DirectoryNameFactory implements DirectoryNameFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function __construct(
        EntityValidatorInterface $validator,
        ViolationFormatterInterface $violationFormatter,
        FileOperationsInterface $fileOperations
    ) {
        $this->validator = $validator;
        $this->violationFormatter = $violationFormatter;
        $this->fileOperations = $fileOperations;
    }

    public function createDirectoryName(string $directoryName, bool $checkExistence = false): DirectoryNameInterface
    {
        $name = new DirectoryName(rtrim($directoryName, '/') . '/');

        $violations = $this->validator->validate($name);

        if (\count($violations) > 0) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Given invalid directory name: %s.',
                    $this->violationFormatter->formatViolations($violations)
                )
            );
        }

        if ($checkExistence && !$this->fileOperations->isDirectory($directoryName)) {
            throw new InvalidConfigurationException(
                sprintf('Directory "%s" does not exist.', $directoryName)
            );
        }

        return $name;
    }
}
