<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Helper;

use Magento\Customer\Model\SessionFactory as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Block\Customer\Wishlist;
use Tigren\Ajaxsuite\Helper\Data as AjaxsuiteHelper;

/**
 * Class Data
 *
 * @package Tigren\Ajaxwishlist\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var
     */
    protected $_storeId;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @var AjaxsuiteHelper
     */
    protected $_ajaxSuite;

    /**
     * @var Wishlist
     */
    protected $_wishList;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishListHelper;

    /**
     * @var
     */
    protected $_productIds;

    /**
     * Data constructor.
     *
     * @param Context                       $context
     * @param StoreManagerInterface         $storeManager
     * @param Registry                      $coreRegistry
     * @param CustomerSession               $customerSession
     * @param LayoutFactory                 $layoutFactory
     * @param EncoderInterface              $jsonEncoder
     * @param DecoderInterface              $jsonDecoder
     * @param AjaxsuiteHelper               $ajaxSuite
     * @param Wishlist                      $wishList
     * @param \Magento\Wishlist\Helper\Data $wishListHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry,
        CustomerSession $customerSession,
        LayoutFactory $layoutFactory,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        AjaxsuiteHelper $ajaxSuite,
        Wishlist $wishList,
        \Magento\Wishlist\Helper\Data $wishListHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_layoutFactory = $layoutFactory;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_ajaxSuite = $ajaxSuite;
        $this->_wishList = $wishList;
        $this->_wishListHelper = $wishListHelper;
    }

    /**
     * @return string
     */
    public function getAjaxWishlistInitOptions()
    {
        $optionsAjaxsuite = $this->_jsonDecoder->decode($this->_ajaxSuite->getAjaxSuiteInitOptions());
        $customerId = $this->_customerSession->create()->getCustomer()->getId();
        $options = [
            'ajaxWishlist' => [
                'enabled' => $this->isEnabledAjaxWishlist(),
                'ajaxWishlistUrl' => $this->_getUrl('ajaxwishlist/wishlist/showPopup'),
                'loginUrl' => $this->_getUrl('customer/account/login'),
                'customerId' => $customerId,
            ],
        ];
        return $this->_jsonEncoder->encode(array_merge($optionsAjaxsuite, $options));
    }


    /**
     * @return bool
     */
    public function isEnabledAjaxWishlist()
    {
        return (bool)$this->scopeConfig->getValue(
            'ajaxwishlist/general/enabled',
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getOptionsPopupHtml()
    {
        $layout = $this->_layoutFactory->create();
        $update = $layout->getUpdate();
        $update->load('ajaxwishlist_options_popup');
        $layout->generateXml();
        $layout->generateElements();
        return $layout->getOutput();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getSuccessHtml()
    {
        $layout = $this->_layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->addHandle('ajaxwishlist_success_message')->load();
        $layout->generateXml();
        $layout->generateElements();
        $result = $layout->getOutput();
        $layout->__destruct();
        return $result;
    }

    /**
     * @param  $product
     * @return string
     * @throws LocalizedException
     */
    public function getErrorHtml($product)
    {
        $layout = $this->_layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->addHandle('ajaxwishlist_error_message')->load();
        $layout->generateXml();
        $layout->generateElements();
        $result = $layout->getOutput();
        $layout->__destruct();
        return $result;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getSuccessRemoveHtml()
    {
        $layout = $this->_layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->addHandle('ajaxwishlist_remove_success_message')->load();
        $layout->generateXml();
        $layout->generateElements();
        $result = $layout->getOutput();
        $layout->__destruct();
        return $result;
    }
}
