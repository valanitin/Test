<?php
/**
 * Magentizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magentizer.com license that is
 * available through the world-wide-web at this URL:
 * https://www.Magentizer.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magentizer
 * @package     Magentizer_GeoIP
 * @copyright   Copyright (c) Magentizer (https://www.Magentizer.com/)
 * @license     https://www.Magentizer.com/LICENSE.txt
 */

namespace Magentizer\GeoIP\Controller\Adminhtml\System\Config;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magentizer\GeoIP\Helper\Data as HelperData;
use PharData;
use function stream_get_wrappers;
use function stream_wrapper_restore;

/**
 * Class Geoip
 * @package Magentizer\GeoIP\Controller\Adminhtml\System\Config
 */
class Geoip extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Geoip constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DirectoryList $directoryList
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DirectoryList $directoryList,
        HelperData $helperData
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_directoryList    = $directoryList;
        $this->_helperData       = $helperData;

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        
        $status = false;
        try {
            $path = $this->_directoryList->getPath('var') . '/Magentizer/GeoIp/GeoIp';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $folder   = scandir($path, true);
            $pathFile = $path . '/' . $folder[0] . '/GeoLite2-City.mmdb';
            if (file_exists($pathFile)) {
                foreach (scandir($path . '/' . $folder[0], true) as $filename) {
                    if ($filename == '..' || $filename == '.') {
                        continue;
                    }
                    unlink($path . '/' . $folder[0] . '/' . $filename);
                }
                rmdir($path . '/' . $folder[0]);
            }

            file_put_contents($path . '/GeoLite2-City.tar.gz', fopen($this->_helperData->getDownloadPath(), 'r'));
            if (!in_array('phar', stream_get_wrappers(), true)) {
                stream_wrapper_restore('phar');
            }

            $phar = new PharData($path . '/GeoLite2-City.tar.gz');
            $phar->extractTo($path);
            $status  = true;
            $message = __('Download library success!');
        } catch (Exception $e) {
            $message = __('Can\'t download file. Please try again! %1', $e->getMessage());
        }

        /** @var Json $result */
        $result = $this->resultJsonFactory->create();
        
        return $result->setData(['success' => $status, 'message' => $message]);
    }
}
