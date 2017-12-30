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
use Strider2038\ImgCache\Utility\ViolationFormatterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class PlainFilenameParser implements PlainFilenameParserInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationsFormatter;

    public function __construct(
        EntityValidatorInterface $validator,
        ViolationFormatterInterface $violationsFormatter
    ) {
        $this->validator = $validator;
        $this->violationsFormatter = $violationsFormatter;
    }

    public function getParsedFilename(string $filename): PlainFilename
    {
        $plainFilename = new PlainFilename($filename);
        $violations = $this->validator->validate($plainFilename);

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
