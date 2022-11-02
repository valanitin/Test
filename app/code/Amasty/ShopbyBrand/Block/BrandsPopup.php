<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Block;

class BrandsPopup extends \Amasty\ShopbyBrand\Block\Widget\BrandList
{
    /**
     * @var string
     */
    protected $_template = 'brands_popup.phtml';

    /**
     * @return bool
     */
    public function isShowPopup()
    {
        return $this->brandHelper->getModuleConfig('general/brands_popup');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->brandHelper->getBrandLabel();
    }

    /**
     * @return string
     */
    public function getAllbrandsUrl()
    {
        return $this->brandHelper->getAllBrandsUrl();
    }

    /**
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface[]|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllBrands()
    {
        return $this->brandHelper->getBrandOptions();
    }

    /**
     * @return bool
     */
    public function isAllBrandsPage()
    {
        if ($this->getRequest()->getOriginalPathInfo()) {
            $isAllBrandsPage = strpos(
                    $this->brandHelper->getAllBrandsUrl(),
                    $this->getRequest()->getOriginalPathInfo()
                ) !== false;
        } else {
            $isAllBrandsPage = false;
        }

        return $isAllBrandsPage;
    }
}
