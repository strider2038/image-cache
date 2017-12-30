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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFilenameFactory implements ImageFilenameFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    public function __construct(EntityValidatorInterface $validator, ViolationFormatterInterface $violationFormatter)
    {
        $this->validator = $validator;
        $this->violationFormatter = $violationFormatter;
    }

    public function createImageFilenameFromRequest(RequestInterface $request): ImageFilenameInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $filename = new ImageFilename(ltrim($path, '/'));

        $violations = $this->validator->validate($filename);

        if (\count($violations) > 0) {
            throw new InvalidRequestValueException(
                sprintf(
                    'Given invalid image filename: %s.',
                    $this->violationFormatter->formatViolations($violations)
                )
            );
        }

        return $filename;
    }
}
