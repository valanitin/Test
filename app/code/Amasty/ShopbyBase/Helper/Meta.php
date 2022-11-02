<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Helper;

use Magento\Framework\Registry;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Meta extends AbstractHelper
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @return string
     */
    public function getOriginPageMetaTitle($category)
    {
        return $category->getData('meta_title')
            ? $category->getData('meta_title')
            : (string) $this->registry->registry(\Amasty\ShopbyBase\Plugin\View\Page\Title::PAGE_META_TITLE_MAIN_PART);
    }

    /**
     * @return string
     */
    public function getOriginPageMetaDescription($category)
    {
        return $category->getData('meta_description')
            ? $category->getData('meta_description')
            : $this->pageConfig->getDescription();
    }
}
