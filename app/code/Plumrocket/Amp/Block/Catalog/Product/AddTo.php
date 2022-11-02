<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog\Product;

class AddTo extends \Magento\Catalog\Block\Product\View
{
    const WISHLIST_MESSAGE_BLOCK_NAME = 'amp.product.form.wishlist.message';
    const COMPARE_MESSAGE_BLOCK_NAME = 'amp.product.form.compare.message';

    /**
     * @var null|\Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * AddTo constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context              $context
     * @param \Magento\Framework\Url\EncoderInterface             $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils               $string
     * @param \Magento\Catalog\Helper\Product                     $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface           $localeFormat
     * @param \Magento\Customer\Model\Session                     $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface   $priceCurrency
     * @param \Plumrocket\Amp\Helper\Data                         $dataHelper
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->isWishListAllowed() || $this->isCompareAllowed();
    }

    /**
     * Disable render if both button disabled
     *
     * @return string
     */
    protected function _toHtml() // @codingStandardsIgnoreLine
    {
        if (! $this->canShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve amp add to wish list url
     *
     * @return string
     */
    public function getWishListUrl()
    {
        return $this->getUrl('pramp/api_wishlist/add', ['ajax' => true]);
    }

    /**
     * Retrieve amp add to compare url
     *
     * @return string
     */
    public function getCompareUrl()
    {
        return $this->getUrl('pramp/api_compare/add', ['ajax' => true]);
    }

    /**
     * Use magento config for display wish list
     *
     * @return bool
     */
    public function isWishListAllowed()
    {
        return $this->_request->isSecure() && $this->_wishlistHelper->isAllow();
    }

    /**
     * Method for rewrite, or another logic
     *
     * @return bool
     */
    public function isCompareAllowed()
    {
        return $this->_request->isSecure();
    }

    /**
     * @return string
     */
    public function getWishlistFormMessageEvents()
    {
        return $this->getAmpWishlistMessagesBlock()->getFormMessageEvents();
    }

    /**
     * @return string
     */
    public function getCompareFormMessageEvents()
    {
        return $this->getAmpCompareMessagesBlock()->getFormMessageEvents();
    }

    /**
     * @return bool|\Plumrocket\Amp\Block\Page\Form\Message
     */
    protected function getAmpWishlistMessagesBlock() // @codingStandardsIgnoreLine
    {
        return $this->getLayout()->getBlock(self::WISHLIST_MESSAGE_BLOCK_NAME);
    }

    /**
     * @return bool|\Plumrocket\Amp\Block\Page\Form\Message
     */
    protected function getAmpCompareMessagesBlock() // @codingStandardsIgnoreLine
    {
        return $this->getLayout()->getBlock(self::COMPARE_MESSAGE_BLOCK_NAME);
    }
}
