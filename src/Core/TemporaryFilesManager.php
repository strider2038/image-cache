<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Helper\FileHelper;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TemporaryFilesManager extends Component implements TemporaryFilesManagerInterface
{
    /** @var string Root directory for temporary files */
    protected $directory;

    public function __construct(Application $app, $directory = '/tmp/imgcache')
    {
        parent::__construct($app);
        FileHelper::createDirectory($directory);
        if (substr($directory, -1) !== '/') {
            $directory .= '/';
        }
        $this->directory = $directory;
        if (!is_dir($this->directory)) {
            throw new ApplicationException("Cannot create directory '{$directory}'");
        }
    }
    
    public function getHashedName(string $fileKey): string
    {
        $ext = pathinfo($fileKey, PATHINFO_EXTENSION);
        if (!empty($ext)) {
            $ext = '.' . $ext;
        }
        return $this->directory . md5($fileKey) . $ext;
    }
    
    public function getFilename(string $fileKey): ?string
    {
        $hashName = $this->getHashedName($fileKey);
        return file_exists($hashName) ? $hashName : null;
    }

    public function putFile(string $fileKey, $data): string
    {
        $hashName = $this->getHashedName($fileKey);
        if (file_put_contents($hashName, $data) !== false) {
            return $hashName;
        }
        throw new ApplicationException("Cannot create file '{$hashName}'");
    }
}
