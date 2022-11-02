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
namespace Bss\GeoIPAutoSwitchStore\Observer;

use Magento\Framework\Event\ObserverInterface;

class HideStoreView implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * HideStoreView constructor.
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ipForTester = null;
        $ipForTester = $this->request->getParam('ipTester');
        $ipCustomer = $this->dataHelper->getIpCustomer($ipForTester);

        $countIp = $this->restrictionIp($ipCustomer);
        $userBots = $this->dataHelper->restrictionUserAgent($this->getStoreId());
        $countUserBot = $this->setCounterUserBot($userBots);

        $layout = $observer->getData('layout');
        $alowSwitch = $this->dataHelper->getAllowSwitch($this->getStoreId());
        $moduleEnable = $this->dataHelper->getEnableModule();
        if ($alowSwitch == '0' && $moduleEnable == '1' && $countIp !== 1 && $countUserBot !== 1) {
            $layout->getUpdate()->addHandle('hide_storeview');
        }
        return $this;
    }

    /**
     * @param string $userBots
     * @return int
     */
    protected function setCounterUserBot($userBots)
    {
        $countUserBot = 0;
        $http_user_agent = $this->dataHelper->returnHttpUserAgent();
        if ($userBots != null && $userBots != '' && (bool)$http_user_agent) {
            $userBots = explode(',', $userBots);
            foreach ($userBots as $userBot) {
                $userBot = rtrim($userBot, ' ');
                $userBot = ltrim($userBot, ' ');
                if (strstr(strtolower($http_user_agent), strtolower($userBot))) {
                    $countUserBot = 1;
                }
            }
        }
        return $countUserBot;
    }

    /**
     * @param string $ipCustomer
     * @return int
     */
    protected function restrictionIp($ipCustomer)
    {
        $restrictionIps = $this->dataHelper->restrictionIp($this->storeManager->getStore()->getId());
        $countIp = 0;

        if ($restrictionIps != null && $restrictionIps != '') {
            $restrictionIps = explode("\n", $restrictionIps);
        
            foreach ($restrictionIps as $restrictionIp) {
                $restrictionIp = rtrim($restrictionIp, "\n");
                $restrictionIp = rtrim($restrictionIp, "\r");
                $restrictionIp = rtrim($restrictionIp, " ");
                $restrictionIp = ltrim($restrictionIp, "\n");
                $restrictionIp = ltrim($restrictionIp, "\r");
                $restrictionIp = ltrim($restrictionIp, ' ');
                if ($ipCustomer == $restrictionIp) {
                    $countIp = 1;
                }
            }
        }
        return $countIp;
    }
}
