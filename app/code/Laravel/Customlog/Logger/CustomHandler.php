<?php

namespace Laravel\Customlog\Logger;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class CustomHandler extends Base
{

	/**
     * @inheritDoc
     */
    public function write(array $record)
    {
        $logDir = $this->filesystem->getParentDirectory($this->url);
        if (!$this->filesystem->isDirectory($logDir)) {
            $this->filesystem->createDirectory($logDir);
        }
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/front-templog.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);

		$logger->info("Info".json_encode($record));

        parent::write($record);
    }

}