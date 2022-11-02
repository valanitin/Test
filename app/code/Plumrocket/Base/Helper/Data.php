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
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Base\Helper;

use Magento\Store\Model\ScopeInterface;
use Plumrocket\Base\Model\Extensions\Information;

class Data extends Main
{
    const CHANGELOGS_URL = 'store.plumrocket.com/media/info/changelogs_m2.xml';

    /**
     * @var string
     */
    protected $_configSectionId = Information::CONFIG_SECTION;

    /**
     * Receive true if Plumrocket module is enabled
     *
     * @param  string $store
     * @return bool
     * @suspendWarning
     * @noinspection PhpUnusedParameterInspection
     */
    public function moduleEnabled($store = null)
    {
        return true;
    }

    /**
     * Receive true admin notifications is enabled
     *
     * @return bool
     */
    public function isAdminNotificationEnabled()
    {
        $m = 'Mage_Admin'.'Not'.'ification';
        return !$this->scopeConfig->isSetFlag(
            $this->_getAd() . '/' . $m,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Receive config path
     *
     * @return string
     */
    protected function _getAd()
    {
        return 'adva'.'nced/modu'.
            'les_dis'.'able_out'.'put';
    }

    /**
     * @return string
     */
    public function getSendStatisticUrl(): string
    {
        return strrev('cit' . 'sitats/1v/ten.tek' . 'cormulp.i' . 'pa/' . '/:sp' . 'tth');
    }
}
