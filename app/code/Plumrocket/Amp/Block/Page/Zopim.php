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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page;

use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\UrlInterface;

class Zopim extends \Magento\Framework\View\Element\Template
{
    const LIVECHAT_DOMAIN_BASE_URL = "https://static.zdassets.com/web_widget/latest/liveChat.html";
    const LIVECHAT_BASE_URL = "https://v2.zopim.com/widget/livechat.html";
    const DEFAULT_LANG = 'en';

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * Zopim Domain
     * @var string|null
     */
    private $zopimDomain;

    /**
     * @deprecated use \Plumrocket\Amp\Helper\Data::zopimDomain instead
     * Zopim Key
     * @var string|null
     */
    public $zopimKey = null;

    /**
     * Zopim constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Helper\Data                      $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Retrieve Zopim domain
     *
     * @return string
     */
    public function getZopimDomain()
    {
        if (null === $this->zopimDomain) {
            $this->zopimDomain = $this->dataHelper->getZopimEnabled() ? $this->dataHelper->getZopimDomain() : '';
        }
        return $this->zopimDomain;
    }

    /**
     * @deprecated use \Plumrocket\Amp\Helper\Data::getZopimDomain() instead
     * Retrieve Zopim key
     *
     * @return string
     */
    public function getZopimKey()
    {
        if (null === $this->zopimKey) {
            $this->zopimKey = $this->dataHelper->getZopimEnabled() ? $this->dataHelper->getZopimKey() : '';
        }
        return $this->zopimKey;
    }

    /**
     * Retrieve language code by store current locale options
     * @param void
     * @return string
     */
    public function getLang()
    {
        $store = $this->_storeManager->getStore();
        $locale = $this->_scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $store->getStoreId()
        );

        $localeInfo = explode('_', $locale);
        if (!empty($localeInfo[0])) {
            return $localeInfo[0];
        }

        return self::DEFAULT_LANG;
    }

    /**
     * Retrieve current host name
     * @param void
     * @return string
     */
    public function getHostname()
    {
        $store = $this->_storeManager->getStore();
        $baseUrl = filter_var($store->getBaseUrl(UrlInterface::URL_TYPE_LINK), FILTER_VALIDATE_URL);

        if ($baseUrl && ($hostname = parse_url($baseUrl, PHP_URL_HOST))) {
            return $hostname;
        }

        return null;
    }

    /**
     * Retrieve widget url
     *
     * @return null|string
     */
    public function getWidgetUrl()
    {
        if ($this->getZopimDomain()) {
            $queryData = [
                'key' => $this->getZopimDomain()
            ];

            return self::LIVECHAT_DOMAIN_BASE_URL . '?#' . urldecode(http_build_query($queryData));
        }

        if ($this->getZopimKey()) {
            $queryData = [
                'key' => $this->getZopimKey(),
                'hostname' => $this->getHostname(),
                'lang' => $this->getLang(),
            ];

            return self::LIVECHAT_BASE_URL . '?' . urldecode(http_build_query($queryData));
        }

        return null;
    }

    /**
     * Retrieve button label
     * If label is empty, return default label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return ($this->dataHelper->getZopimButtonLabel() ?: __('Live Chat'));
    }
}
