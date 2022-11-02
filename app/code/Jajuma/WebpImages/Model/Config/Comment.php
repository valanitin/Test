<?php

namespace Jajuma\WebpImages\Model\Config;

class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    protected $dir;

    public function __construct(\Magento\Framework\Filesystem\DirectoryList $dir)
    {
        $this->dir = $dir;
    }

    public function getCommentText($elementValue)
    {
        return 'Define the specify path of cwebp command or leave it empty to use global command "cwebp". Example: ' . $this->dir->getPath('app') . '/code/Jajuma/WebpImages/bin/cwebp';
    }
}