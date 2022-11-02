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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class ListSlider
 *
 * @method \Magento\Catalog\Model\ResourceModel\Product\Collection|null getProductCollection()
 * @method integer|null getProductsCount()
 * @method $this setProductsCount($number)
 */
class Category extends \Magento\CatalogWidget\Block\Product\ProductsList implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Widget identificator
     */
    const WIDGET_TYPE = 'amp_category_list';

    /**
     * This constant does not set the default value for the "Number of Products to Display" option
     */
    const DEFAULT_PRODUCTS_COUNT = 5;

    /**
     * Default sort by for product collection and default value for option
     */
    const DEFAULT_COLLECTION_SORT_BY = 'name';

    /**
     * Default sort order for product collection and default value for option
     */
    const DEFAULT_COLLECTION_ORDER = 'asc';

    /**
     * Default value for option "Display Add To Cart Button"
     */
    const DEFAULT_SHOW_ADD_TO_CART = false;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var bool|null
     */
    private $canShow;

    /**
     * @return string
     */
    public function getType()
    {
        return self::WIDGET_TYPE;
    }

    /**
     * Category constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context                         $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context                            $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder                      $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule                              $rule
     * @param \Magento\Widget\Helper\Conditions                              $conditionsHelper
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface               $categoryRepository
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        // TODO: remove this fix after left support 2.3.4 or lower
        $arguments = [
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper
        ];

        if (version_compare($productMetadata->getVersion(), '2.3.4', '>')) {
            $arguments[] = $categoryRepository;
        }

        $arguments[] = $data;

        parent::__construct(...$arguments);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Retrieve product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function createCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage(1)
            ->setOrder($this->getSortBy(), $this->getSortOrder());

        $collection->addCategoryFilter($this->getCategory());

        return $collection;
    }

    /**
     * @return bool|null
     */
    private function canShow()
    {
        if (null === $this->canShow) {
            $this->canShow = null !== $this->getCategory();
        }

        return $this->canShow;
    }

    /**
     * Set default template for widget in cms page
     *
     * @return $this|\Magento\CatalogWidget\Block\Product\ProductsList
     */
    protected function _beforeToHtml()
    {
        if (! $this->canShow()) {
            return $this;
        }

        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::catalog/product/widget/items.phtml');
        }

        return parent::_beforeToHtml();
    }

    /**
     * Disable render for invalid widgets
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->canShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Add cache key info
     *
     * @return array|int
     */
    public function getCacheKeyInfo()
    {
        $baseCacheKeyInfo = parent::getCacheKeyInfo();

        array_push($baseCacheKeyInfo, self::WIDGET_TYPE, $this->_getWidgetParams(true));

        return $baseCacheKeyInfo;
    }

    /**
     * @param bool $toString
     * @return array|string
     */
    protected function _getWidgetParams($toString = false)
    {
        $params = [
            $this->getCategoryId(),
            $this->getSortBy(),
            $this->getSortOrder(),
            $this->showAddToCart()
        ];

        return $toString ? implode('|', $params) : $params;
    }

    /**
     * Get selected category
     *
     * @return null|\Magento\Catalog\Api\Data\CategoryInterface|\Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        try {
            return $this->categoryRepository->get($this->getCategoryId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * Retrieve count of products
     *
     * @return int
     */
    public function hasItems()
    {
        return $this->getProductCollection()->getSize();
    }

    /**
     * Retrieve number of products to display
     *
     * @return int
     */
    public function getPageSize()
    {
        if ((int)$this->getProductsCount()) {
            return (int)$this->getProductsCount();
        }

        return self::DEFAULT_PRODUCTS_COUNT;
    }

    /**
     * Retrieve category id
     *
     * @return int
     * @throws LocalizedException
     */
    public function getCategoryId()
    {
        $catId = $this->getData('category');
        if ($catId && strpos($catId, '/') !== false) {
            $result = explode('/', $catId);
            $catId = $result[1];
        }

        if (! (int)$catId || ! is_numeric($catId)) {
            throw new LocalizedException(__('Category id is not valid'));
        }

        return (int)$catId;
    }

    /**
     * Retrieve sort by: price, position and etc.
     *
     * @return mixed
     */
    public function getSortBy()
    {
        if (! $this->hasData('sort_by')) {
            $this->setData('sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }

        return $this->getData('sort_by');
    }

    /**
     * Retrieve sort order: asc/desc
     *
     * @return string
     */
    public function getSortOrder()
    {
        if (! $this->hasData('sort_order')) {
            $this->setData('sort_order', self::DEFAULT_COLLECTION_ORDER);
        }

        return $this->getData('sort_order');
    }

    /**
     * @return bool
     */
    public function showAddToCart()
    {
        if (! $this->hasData('show_add_to_cart')) {
            $this->setData('show_add_to_cart', self::DEFAULT_SHOW_ADD_TO_CART);
        }

        return (bool)$this->getData('show_add_to_cart');
    }
}
