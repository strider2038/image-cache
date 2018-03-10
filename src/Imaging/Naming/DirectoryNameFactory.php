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

use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DirectoryNameFactory implements DirectoryNameFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createDirectoryName(string $directoryName): DirectoryNameInterface
    {
        $name = new DirectoryName(rtrim($directoryName, '/') . '/');
        $this->validator->validateWithException($name, InvalidConfigurationException::class);

        return $name;
    }
}
