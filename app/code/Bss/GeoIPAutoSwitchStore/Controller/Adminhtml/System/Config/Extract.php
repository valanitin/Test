<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use \ZipArchive;

class Extract extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Archive\Zip
     */
    protected $zip;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig
     */
    protected $geoIpConfig;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var ZipArchive
     */
    protected $zipCore;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * Extract constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ZipArchive $zipCore
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig $geoIpConfig
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper
     * @param \Magento\Framework\Archive\Zip $zip
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ZipArchive $zipCore,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig $geoIpConfig,
        \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper,
        \Magento\Framework\Archive\Zip $zip,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->dataHelper = $dataHelper;
        $this->geoIpConfig = $geoIpConfig;
        $this->catalogSession = $context->getSession();
        $this->zip = $zip;
        $this->zipCore = $zipCore;
        $this->directoryList = $directoryList;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->file = $file;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $fileCustom = $this->dataHelper->getFileCustom();

        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {

            $myResult = 'False';

            $rootDir = $this->directoryList->getRoot();
            $fileName = $rootDir . "/var/bss_import/GeoIPCountryCSV.zip";

            $newFileName = $rootDir . "/var/bss_import";

            $customFile = $rootDir . "/pub/media/bss_upload/file/" . $fileCustom;

            if ($fileCustom != '' || $fileCustom != null) {
                $fileName = $customFile;
            }

            if ($this->file->isExists($fileName)) {

                $myFile = $this->unpack($fileName, $newFileName);

                if ($myFile) {
                    $myResult = 'Done';
                    try {
                        $this->geoIpConfig->deleteValue('status');
                        $this->geoIpConfig->saveValue('pending', 'status');
                    } catch (\Exception $exception) {
                        throw new \LogicException(__($exception->getMessage()));
                    }
                    return $result->setData($myResult);
                } else {
                    return $result->setData($myResult);
                }
            } else {
                return $result->setData($myResult);
            }
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_GeoIPAutoSwitchStore::update');
    }

    /**
     * Unpack file.
     *
     * @param string $source
     * @param string $destination
     *
     * @return string
     */
    protected function unpack($source, $destination)
    {
        $zip = $this->zipCore;
        $zip->open($source);
        $zip->extractTo($destination);
        $zip->close();
        return $destination;
    }
}
