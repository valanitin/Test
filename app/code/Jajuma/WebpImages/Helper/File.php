<?php

namespace Jajuma\WebpImages\Helper;

class File extends \Magento\Framework\Filesystem\Io\File
{
    public function setIwd($iwd)
    {
        $this->_iwd = $iwd;
    }

    public function setCwd($cwd)
    {
        $this->_cwd = $cwd;
    }
}
