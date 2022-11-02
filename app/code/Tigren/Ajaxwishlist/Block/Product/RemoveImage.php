<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Block\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Image
 *
 * @package Tigren\Ajaxwishlist\Block\Product
 */
class RemoveImage extends Template
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $prdImageHelper;

    /**
     * Image constructor.
     *
     * @param Context                       $context
     * @param Registry                      $registry
     * @param ObjectManagerInterface        $objectManager
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array                         $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->prdImageHelper = $imageHelper;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        $color = $this->_request->getParam('color');
        $configurablePrdModel = $this->_objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
        $attributeOptions = [93 => $color];
        $prdId = $this->_coreRegistry->registry('current_product')->getId();
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($prdId);
        $imageUrl = $this->getProductImageUrl($product, 'category');
        return $imageUrl;
    }

    /**
     * @param  $product
     * @param  $size
     * @return string
     */
    public function getProductImageUrl($product, $size)
    {
        $imageSize = 'product_page_image_' . $size;
        if ($size == 'category') {
            $imageSize = 'category_page_list';
        }
        $imageUrl = $this->prdImageHelper->init($product, $imageSize)
            ->keepAspectRatio(true)
            ->keepFrame(false)
            ->getUrl();
        return $imageUrl;
    }
}
