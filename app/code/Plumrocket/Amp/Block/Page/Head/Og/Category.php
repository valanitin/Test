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

namespace Plumrocket\Amp\Block\Page\Head\Og;

class Category extends AbstractOg
{
    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Plumrocket\Amp\Helper\Data $helper,
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Plumrocket\Amp\Helper\Data $helper,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $helper, $data);
        $this->_categoryHelper = $categoryHelper;
    }

    /**
     * Retrieve additional data
     * @return array
     */
    public function getOgParams()
    {
        $params = parent::getOgParams();
        $_category = $this->getCategory();

        return array_merge($params, [
            'type' => 'category',
            'url' => $this->_helper->getCanonicalUrl($_category->getUrl()),
            'image' => (string)$_category->getImageUrl(),
        ]);
    }

    /**
     * Retrieve current category object
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }
}
