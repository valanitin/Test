<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Debug;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

class Logger
{
    const LOG_FILE = 'pslogin.log';

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    private $directory;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $dir;

    /**
     * Logger constructor.
     *
     * @param \Magento\Framework\Filesystem               $filesystem
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filesystem\DirectoryList $dir
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::LOG);
        $this->dir = $dir;
    }

    /**
     * @param $message
     * @return $this
     */
    public function debug($message)
    {
        try {
            $this->directory->writeFile(self::LOG_FILE, $message, 'a+');
        } catch (FileSystemException $e) {
            return $this;
        }

        return $this;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getAbsoluteFilePath(): string
    {
        return $this->dir->getPath(DirectoryList::LOG) . DIRECTORY_SEPARATOR . self::LOG_FILE;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getRelativeFilePath(): string
    {
        return str_replace($this->dir->getRoot(), '', $this->getAbsoluteFilePath());
    }
}
