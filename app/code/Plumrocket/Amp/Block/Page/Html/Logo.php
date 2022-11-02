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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Html;

class Logo extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * Retrieve logo image URL
     * @return string
     */
    protected function _getLogoUrl()
    {
        $folderName = 'pramp/logo';
        $storeLogoPath = $this->_scopeConfig->getValue(
            'pramp/logo/image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;

        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } else {
            $url = parent::_getLogoUrl();
        }

        return $url;
    }

    /**
     * Retrieve logo width
     * @return int
     */
    public function getLogoWidth()
    {
        if (empty($this->_data['logo_width'])) {
            $this->_data['logo_width'] = $this->_scopeConfig->getValue(
                'pramp/logo/logo_width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return (int)$this->_data['logo_width'] ? : (int)$this->getLogoImgWidth();
    }

    /**
     * Retrieve logo height
     * @return int
     */
    public function getLogoHeight()
    {
        if (empty($this->_data['logo_height'])) {
            $this->_data['logo_height'] = $this->_scopeConfig->getValue(
                'pramp/logo/logo_height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return (int)$this->_data['logo_height'] ? : (int)$this->getLogoImgHeight();
    }

}