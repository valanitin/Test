<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magetracer\StoreOptimization\Cron;

class ImageCompress
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
		system('php -dmemory_limit=6G bin/magento image:compress webp -p pub/media/weltpixel && php -dmemory_limit=6G bin/magento image:compress webp -p pub/media/wysiwyg');
        $this->logger->addInfo("Cronjob ImageCompress is executed.");
    }
}

