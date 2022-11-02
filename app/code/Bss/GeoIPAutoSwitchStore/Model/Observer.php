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
namespace Bss\GeoIPAutoSwitchStore\Model;

use Magento\Framework\Notification\NotifierInterface as NotifierPool;

class Observer
{
    /**
     * @var \Magento\Backend\Model\Auth\SessionFactory
     */
    protected $authSession;

    /**
     * @var ResourceModel\DeleteMaxMind
     */
    protected $deleteMaxMind;

    /**
     * @var ResourceModel\GeoIpConfig
     */
    protected $geoIpConfig;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var ResourceModel\SaveMaxMindDatabase
     */
    protected $saveMaxMind;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp
     */
    protected $updateStatus;

    /**
     * @var NotifierPool
     */
    protected $notifierPool;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Observer constructor.
     * @param ResourceModel\GeoIpConfig $geoIpConfig
     * @param \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp $updateStatus
     * @param ResourceModel\DeleteMaxMind $deleteMaxMind
     * @param ResourceModel\SaveMaxMindDatabase $saveMaxMind
     * @param \Magento\Backend\Model\Auth\SessionFactory $authSession
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param NotifierPool $notifierPool
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpConfig $geoIpConfig,
        \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp $updateStatus,
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\DeleteMaxMind $deleteMaxMind,
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\SaveMaxMindDatabase $saveMaxMind,
        \Magento\Backend\Model\Auth\SessionFactory $authSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        NotifierPool $notifierPool,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->geoIpConfig = $geoIpConfig;
        $this->updateStatus = $updateStatus;
        $this->deleteMaxMind = $deleteMaxMind;
        $this->saveMaxMind = $saveMaxMind;
        $this->authSession = $authSession;
        $this->directoryList = $directoryList;
        $this->notifierPool = $notifierPool;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            if ($this->getStatus()) {
                $geoIpStatus = $this->getStatus();
                if ($geoIpStatus == 'pending') {
                    $this->geoIpConfig->deleteValue('status');
                    $this->geoIpConfig->saveValue('doing', 'status');

                    //Delete Old database
                    $this->deleteMaxMind->deleteValue();

                    //Import new database
                    $rootDir = $this->directoryList->getRoot();
                    $newFileName = $rootDir."/var/bss_import";

                    $saveMaxMind = $this->saveMaxMind->import($newFileName);

                    //After import
                    if ($saveMaxMind['status'] == 'Done') {
                        $myResult['status'] = 'Done';
                        $myResult['time'] = $saveMaxMind['time'];
                        $this->notifierPool->addNotice(
                            'Update MaxMind GEOIP Country to Database success',
                            'Update success at '.$saveMaxMind['time']
                        );
                    } else {
                        $this->notifierPool->addCritical(
                            'Update MaxMind GEOIP Country to Database false',
                            'Update failed, please try again!'
                        );
                        $myResult['status'] = 'False';
                    }

                    //Change status
                    $this->geoIpConfig->deleteValue('status');
                    $this->geoIpConfig->saveValue('done', 'status');
                }
            } else {
                $this->geoIpConfig->saveValue('done', 'status');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return $this;
    }

    /**
     * @return array|bool
     */
    public function getStatus()
    {
        if ($this->updateStatus->getSelectingData('status')) {
            return $this->updateStatus->getSelectingData('status');
        } else {
            return false;
        }
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    public function getCurrentUser()
    {
        return $this->authSession->create()->getUser();
    }
}
