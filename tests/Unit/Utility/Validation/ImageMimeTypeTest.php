<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility\Validation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\Validation\ImageMimeType;

class ImageMimeTypeTest extends TestCase
{
    private const DEFAULT_MIME_TYPES = ['image/jpeg', 'image/png'];
    private const CUSTOM_MIME_TYPES = ['image/jpeg'];

    /** @test */
    public function construct_noParameters_defaultMimeTypesAreValid(): void
    {
        $constraint = new ImageMimeType();

        $mimeTypes = $constraint->mimeTypes;

        $this->assertEquals(self::DEFAULT_MIME_TYPES, $mimeTypes);
    }

    /** @test */
    public function construct_givenMimeTypes_mimeTypesAreSet(): void
    {
        $constraint = new ImageMimeType([
            'mimeTypes' => self::CUSTOM_MIME_TYPES
        ]);

        $mimeTypes = $constraint->mimeTypes;

        $this->assertEquals(self::CUSTOM_MIME_TYPES, $mimeTypes);
    }
}
