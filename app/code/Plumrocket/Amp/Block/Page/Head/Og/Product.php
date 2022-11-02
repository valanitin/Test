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

use Plumrocket\Amp\Block\Page\Head\Og\AbstractOg as AbstractOg;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Plumrocket\Amp\Helper\Data $helper,
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\Amp\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
    }

    /**
     * Retrieve additional data
     * @return array
     */
    public function getOgParams()
    {
        $params = parent::getOgParams();
        $_product = $this->getProduct();

        return array(
            'type' => 'product',
            'url' => $this->_helper->getCanonicalUrl($_product->getProductUrl()),
            'image' => $this->getImage($_product, 'product_page_image_large', [])->getData('image_url'),
            'title' => $this->pageConfig->getTitle()->get(),
            'description' => mb_substr($this->pageConfig->getDescription(), 0, 200, 'UTF-8'),
        );
    }
}
