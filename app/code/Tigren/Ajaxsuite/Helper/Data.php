<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxsuite\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Tigren\Ajaxsuite\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const IS_ENABLED = 'ajaxsuite/general/enabled';
    /**
     *
     */
    const ISTTL = 'ajaxsuite/general/enabled_popupttl';
    /**
     *
     */
    const TTL = 'ajaxsuite/general/popupttl';
    /**
     *
     */
    const THEME = 'ajaxsuite/effects/theme';
    /**
     *
     */
    const THEME_CUSTOM_BACKGROUND = 'ajaxsuite/effects/color_background';
    /**
     *
     */
    const THEME_CUSTOM_TITLE = 'ajaxsuite/effects/color_title';
    /**
     *
     */
    const ANIMATION = 'ajaxsuite/effects/animation';
    /**
     * @var
     */
    protected $_storeId;
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;
    /**
     * @var LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * Data constructor.
     *
     * @param Context          $context
     * @param CustomerSession  $customerSession
     * @param LayoutFactory    $layoutFactory
     * @param EncoderInterface $jsonEncoder
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        LayoutFactory $layoutFactory,
        EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_layoutFactory = $layoutFactory;
    }

    /**
     * @param  $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxSuiteInitOptions()
    {
        $options = [
            'ajaxSuite' => [
                'enabled' => $this->isEnabledAjaxSuite(),
                'popupTTL' => $this->getTTLAjaxSuite(),
                'animation' => $this->getAnimationAjaxSuite(),
                'backgroundColor' => $this->getScopeConfig('ajaxsuite/effects/background_color'),
                'headerSuccessColor' => $this->getScopeConfig('ajaxsuite/effects/header_success_color'),
                'headerErrorColor' => $this->getScopeConfig('ajaxsuite/effects/header_error_color'),
                'headerTextColor' => $this->getScopeConfig('ajaxsuite/effects/header_text_color'),
                'buttonTextColor' => $this->getScopeConfig('ajaxsuite/effects/button_text_color'),
                'buttonBackgroundColor' => $this->getScopeConfig('ajaxsuite/effects/button_background_color'),
            ]
        ];

        return $this->_jsonEncoder->encode($options);
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxSuite()
    {
        return (bool)$this->scopeConfig->getValue(
            self::IS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * @return int|null
     */
    public function getTTLAjaxSuite()
    {
        if ($this->isEnabledTTLAjaxSuite()) {
            return (int)$this->scopeConfig->getValue(
                self::TTL,
                ScopeInterface::SCOPE_STORE,
                $this->_storeId
            );
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isEnabledTTLAjaxSuite()
    {
        return (bool)$this->scopeConfig->getValue(
            self::ISTTL,
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAnimationAjaxSuite()
    {
        return $this->scopeConfig->getValue(
            self::ANIMATION,
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * @param  $path
     * @return mixed
     */
    public function getScopeConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->_storeId);
    }

    /**
     * @return bool
     */
    public function getLoggedCustomer()
    {
        return (bool)$this->_customerSession->isLoggedIn();
    }
}
