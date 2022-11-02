<?php

namespace Dynamic\SizeApi\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Category
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * Size data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Category $category
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Category $category
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->category = $category;
        parent::__construct($context);
    }

    public function getAttributeData($categoryIds) {

        $data = [];

        if(!empty($categoryIds) && count($categoryIds) > 0) {
            $categoryDataId = '';
            foreach ($categoryIds as $categoryId) {
                $categoryCollection = $this->category->load($categoryId);
                if($categoryCollection->getLevel() == 3 || $categoryCollection->getLevel() == 4) {
                    $categoryDataId = $categoryCollection->getId();
                    break;
                }
            }

            if($categoryId) {
                $systemConfig = $this->getConfig($categoryId);
                if($systemConfig) {
                    $sizeCollection = explode(",", $systemConfig);
                    if(count($sizeCollection) > 0 && !empty($sizeCollection)) {
                        foreach ($sizeCollection as $size) {
                            $data[] = $size;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getConfig($categoryId)
    {
        return $this->scopeConfig->getValue('sizeapi/sizevalue/'.$categoryId, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
