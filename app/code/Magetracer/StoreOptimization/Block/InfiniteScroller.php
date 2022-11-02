<?php
/**
 * Mage Tracer.
 *
 * @category   Magetracer
 * @package    Magetracer_StoreOptimization
 * @author     Magetracer
 * @copyright  Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license    https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Block;

class InfiniteScroller extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magetracer\StoreOptimization\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetracer\StoreOptimization\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * get current toolbar from block
     *
     * @return Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getCurrentPager()
    {
        return $this->getLayout()->getBlock('product_list_toolbar_pager');
    }

    /**
     * can show insection observer on the page
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_helper->getIsScrollerEnable();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canDisplay()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * is lazyload enbled
     *
     * @return boolean
     */
    public function isLazyLoadEnabled()
    {
        return (int)$this->_helper->getIsLazyLoadingEnable();
    }
}
