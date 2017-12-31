<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Filename;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class PlainFilenameParser implements PlainFilenameParserInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getParsedFilename(string $filename): PlainFilename
    {
        $plainFilename = new PlainFilename($filename);
        $this->validator->validateWithException($plainFilename, InvalidRequestValueException::class);

        return $plainFilename;
    }
}
