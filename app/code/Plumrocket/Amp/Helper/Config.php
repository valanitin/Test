<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Cms\Helper\Page;
use Magento\Store\Model\ScopeInterface;

class Config extends \Plumrocket\Base\Helper\Base implements ArgumentInterface
{
    const GROUP_DESIGN = 'front_design';
    const GROUP_HEADER = 'header';

    /**
     * Receive magento config value
     *
     * @param  string      $group
     * @param  string      $path
     * @param  string|int  $scopeCode
     * @param  string|null $scope
     * @return mixed
     */
    public function getConfigByGroup($group, $path, $scopeCode = null, $scope = null)
    {
        return $this->getConfig(
            implode('/', [Data::SECTION_ID, $group, $path]),
            $scopeCode,
            $scope
        );
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getContactTelInHeader($store = null, $scope = null) : string
    {
        return (string)$this->getConfigByGroup(self::GROUP_HEADER, 'tel', $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getHelpMailInHeader($store = null, $scope = null) : string
    {
        return (string)$this->getConfigByGroup(self::GROUP_HEADER, 'mail', $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isEnabledHeaderStoreSwitcher($store = null, $scope = null) : bool
    {
        return (bool)$this->getConfigByGroup(self::GROUP_HEADER, 'store_switcher', $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isEnabledHeaderCurrencySwitcher($store = null, $scope = null) : bool
    {
        return (bool)$this->getConfigByGroup(self::GROUP_HEADER, 'currency_switcher', $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getIconColor($store = null, $scope = null) : string
    {
        return (string)$this->getConfigByGroup(self::GROUP_DESIGN, 'icon_color', $store, $scope);
    }

    /**
     * @return string
     */
     public function getHomePageIdentifier(): string
     {
         $pageId = (string) $this->scopeConfig->getValue(Page::XML_PATH_HOME_PAGE, ScopeInterface::SCOPE_STORE);
         $delimiterPosition = strrpos($pageId, '|');

         if ($delimiterPosition) {
             $pageId = substr($pageId, $delimiterPosition + 1, strlen($pageId));
         }

         return $pageId;
     }
}
