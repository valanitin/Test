<?php
/**
 * @author      FutureSoftIndia <info@futuresoftindia.com>
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\NewsLetterSubscription\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

DEFINE('SAPDS', DIRECTORY_SEPARATOR);

class Handler extends Base
{
    /** @var string */
    protected $loggerType = Logger::DEBUG;

    /** @var string */
    public $fileName = '';

    /** @var string */
    public $cutomfileName = 'NO_PATH';

    /**
     * Constructor
     *
     * @param DriverInterface $filesystem
     * @param Filesystem $corefilesystem
     * @param string $filePath
     */
    public function __construct(
        DriverInterface $filesystem,
        Filesystem $corefilesystem,
        $filePath = null
    ) {
        $corefilesystem = $corefilesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $logpath = $corefilesystem->getAbsolutePath('log' . SAPDS . 'erp' . SAPDS . 'api' . SAPDS);
        $filename = 'ERP__' . Date('Y_m_d') . '.log';
        $filepath = $logpath . $filename;
        $this->cutomfileName = $filepath;
        parent::__construct(
            $filesystem,
            $filepath
        );
    }
}
