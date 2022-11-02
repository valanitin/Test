<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Catalog\Product\ProductList;

use Amasty\Sorting\Helper\Data;
use Magento\Framework\Registry;

class Toolbar
{
    const ALWAYS_DESC = [
        'price_desc'
    ];

    const ALWAYS_ASC = [
        'price_asc'
    ];

    const RELEVANCE_DIRECTION = 'relevance';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Amasty\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar
     */
    private $toolbarModel;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Image
     */
    private $imageMethod;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Instock
     */
    private $stockMethod;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Data $helper,
        \Amasty\Sorting\Model\MethodProvider $methodProvider,
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Amasty\Sorting\Model\ResourceModel\Method\Image $imageMethod,
        \Amasty\Sorting\Model\ResourceModel\Method\Instock $stockMethod,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->methodProvider = $methodProvider;
        $this->toolbarModel = $toolbarModel;
        $this->imageMethod = $imageMethod;
        $this->stockMethod = $stockMethod;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param string                                             $dir
     *
     * @return string
     */
    public function afterGetCurrentDirection($subject, $dir)
    {
        $defaultDir = $this->isDescDir($subject->getCurrentOrder()) ? 'desc' : 'asc';
        $subject->setDefaultDirection($defaultDir);

        if (!$this->toolbarModel->getDirection()
            || $this->shouldSetDirection($subject->getCurrentOrder())
        ) {
            $dir = $defaultDir;
        }

        return $dir;
    }

    /**
     * @param string $order
     *
     * @return bool
     */
    private function isDescDir($order)
    {
        $attributeCodes = $this->helper->getScopeValue('general/desc_attributes');
        $shouldBeDesc = array_merge(self::ALWAYS_DESC, [self::RELEVANCE_DIRECTION]);

        if ($attributeCodes) {
            $shouldBeDesc = array_merge($shouldBeDesc, explode(',', $attributeCodes));
        }

        return in_array($order, $shouldBeDesc);
    }

    /**
     * @param string $order
     *
     * @return bool
     */
    private function shouldSetDirection($order)
    {
        return in_array($order, self::ALWAYS_DESC) || in_array($order, self::ALWAYS_ASC);
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar      $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetCollection($subject, $collection)
    {
        if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            // no image sorting will be the first or the second (after stock). LIFO queue
            $this->imageMethod->apply($collection);
            // in stock sorting will be first, as the method always moves it's paremater first. LIFO queue
            $this->stockMethod->apply($collection);
        }

        return [$collection];
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $result
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function afterSetCollection($subject, $result)
    {
        $this->applyOrdersFromConfig($subject->getCollection());

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    private function applyOrdersFromConfig($collection)
    {
        if ($this->registry->registry(Data::SEARCH_SORTING)) {
            $defaultSortings = $this->helper->getSearchSorting();
        } else {
            $defaultSortings = $this->helper->getCategorySorting();
        }
        // first sorting must be setting by magento as default sorting
        array_shift($defaultSortings);

        foreach ($defaultSortings as $defaultSorting) {
            $dir = $this->isDescDir($defaultSorting) ? 'desc' : 'asc';
            $collection->setOrder($defaultSorting, $dir);
        }
    }
}
