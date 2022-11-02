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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog;

use Plumrocket\Amp\Model\Attribute\Source\Mode;

/**
 * Class View
 * @package Plumrocket\Amp\Block\Catalog
 */
class View extends  \Magento\Catalog\Block\Category\View
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->catalogLayer = $layerResolver->get();
        $this->coreRegistry = $registry;
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    /**
     * Check if category display mode is "Products Only"
     * @return bool
     */
    public function isProductMode()
    {
        if ($this->isDefaultMode()) {
            return parent::isProductMode();
        }
        return $this->getCurrentCategory()->getAmpDisplayMode() == Mode::DM_PRODUCT;
    }

    /**
     * Check if category display mode is "Static Block and Products"
     * @return bool
     */
    public function isMixedMode()
    {
        if ($this->isDefaultMode()) {
            return parent::isMixedMode();
        }
        return $this->getCurrentCategory()->getAmpDisplayMode() == Mode::DM_MIXED;
    }

    /**
     * Check if category display mode is "Static Block Only"
     * For anchor category with applied filter Static Block Only mode not allowed
     *
     * @return bool
     */
    public function isContentMode()
    {
        if ($this->isDefaultMode()) {
            return parent::isContentMode();
        }
        $category = $this->getCurrentCategory();
        $res = false;
        if ($category->getAmpDisplayMode() == Mode::DM_PAGE) {
            $res = true;
            if ($category->getIsAnchor()) {
                $state = $this->_catalogLayer->getState();
                if ($state && $state->getFilters()) {
                    $res = false;
                }
            }
        }
        return $res;
    }

    /**
     * Check if category display mode is "Default"
     *
     * @return boolean
     */
    private function isDefaultMode()
    {
        $currentMode = $this->getCurrentCategory()->getAmpDisplayMode();

        return null === $currentMode || $currentMode === Mode::DM_DEFAULT;
    }
}
