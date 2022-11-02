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

namespace Plumrocket\Amp\Block\Catalog\Product\Widget;

use Magento\Reports\Model\Product\Index\Factory as ProductIndexFactory;

/**
 * Class RecentlyViewed
 *
 * @method string|null  getTitle()
 * @method integer|null getProductsCount()
 * @method $this setProductsCount($number)
 */
class RecentlyViewed extends \Magento\Reports\Block\Product\AbstractProduct implements
    \Magento\Widget\Block\BlockInterface
{
    /**
     * This constant does not set the default value for the "Number of Products to Display" option
     */
    const DEFAULT_PRODUCTS_COUNT = 2;

    const PRODUCTS_COUNT_GET_PARAM = 'count';

    /**
     * @var null|\Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data|null
     */
    private $pricingHelper;

    /**
     * Viewed Product Index type
     *
     * @var string
     */
    protected $_indexType = ProductIndexFactory::TYPE_VIEWED; //@codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * RecentlyViewed constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context          $context
     * @param \Magento\Catalog\Model\Product\Visibility       $productVisibility
     * @param ProductIndexFactory                             $indexFactory
     * @param \Plumrocket\Amp\Helper\Data                     $dataHelper
     * @param \Magento\Framework\Pricing\Helper\Data          $pricingHelper
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array                                           $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Reports\Model\Product\Index\Factory $indexFactory,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $productVisibility, $indexFactory, $data);
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->_storeManager->getStore()->isCurrentlySecure()
            && -1 === version_compare($this->productMetadata->getVersion(), '2.3.0');
    }

    /**
     * Retrieve url for amp-list ajax
     *
     * @return string
     */
    public function getSrcUrl()
    {
        return $this->getUrl('pramp/api/recently', [self::PRODUCTS_COUNT_GET_PARAM => $this->getMaxItems()]);
    }

    /**
     * Get param from widget
     *
     * @return int
     */
    public function getMaxItems()
    {
        if ((int)$this->getProductsCount()) {
            return (int)$this->getProductsCount();
        }

        return self::DEFAULT_PRODUCTS_COUNT;
    }

    /**
     * Set max products count
     * Used only for ajax request
     *
     * @return $this
     */
    public function initPageSize()
    {
        $count = (int)$this->_request->getParam(self::PRODUCTS_COUNT_GET_PARAM);
        $this->setData('page_size', $count ?: self::DEFAULT_PRODUCTS_COUNT);
        return $this;
    }

    /**
     * Retrieve array of products data
     * Used only for ajax request
     *
     * @return array
     */
    public function getList()
    {
        $collection = $this->getItemsCollection();
        $productsInfo = [];
        foreach ($collection as $product) {
            /**
             * @var \Magento\Catalog\Model\Product $product
             */
            $productImage = $this->getImage($product, 'amp_category_page_grid');
            $productsInfo[] = [
                'productName'  => $this->escapeHtml($product->getName()),
                'productUrl'   => $this->dataHelper->getAmpUrl($this->getProductUrl($product)),
                'productPrice' => $this->pricingHelper->currency($product->getFinalPrice(), true, false),
                'imageUrl'     => $productImage->getImageUrl(),
                'imageWidth'   => $productImage->getWidth(),
                'imageHeight'  => $productImage->getHeight(),
                'imageLabel'   => $productImage->stripTags($productImage->getLabel(), null, true),
            ];
        }

        return $productsInfo;
    }

    /**
     * Set default template for widget in cms page
     *
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _beforeToHtml() //@codingStandardsIgnoreLine
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::catalog/product/widget/recently.phtml');
        }

        return parent::_beforeToHtml();
    }
}
