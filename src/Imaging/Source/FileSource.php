<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source;

use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Helper\FileHelper;
use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Core\TemporaryFilesManagerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSource extends AbstractFileSource
{
    /** @var string */
    private $baseDirectory;
    
    function __construct(
        TemporaryFilesManagerInterface $temporaryFilesManager, 
        string $baseDirectory
    ) {
        parent::__construct($temporaryFilesManager);
        $this->baseDirectory = $baseDirectory;
        FileHelper::createDirectory($this->baseDirectory);
        if (!is_dir($this->baseDirectory)) {
            throw new ApplicationException("Cannot create directory '{$this->baseDirectory}'");
        }
    }
    
    public function get(string $filename): Image
    {
        
    }

}
