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
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\KeyValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class PlainFilenameParser implements PlainFilenameParserInterface
{
    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationsFormatter;

    public function __construct(
        ModelValidatorInterface $validator,
        ViolationFormatterInterface $violationsFormatter
    ) {
        $this->validator = $validator;
        $this->violationsFormatter = $violationsFormatter;
    }

    public function getParsedFilename(string $filename): PlainFilename
    {
        $plainFilename = new PlainFilename($filename);
        $violations = $this->validator->validateModel($plainFilename);

        if ($violations->count() > 0) {
            throw new InvalidRequestValueException(
                sprintf(
                    'Filename is not valid: %s',
                    $this->violationsFormatter->formatViolations($violations)
                )
            );
        }

        return $plainFilename;
    }
}
