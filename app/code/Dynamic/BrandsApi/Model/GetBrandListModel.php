<?php

namespace Dynamic\BrandsApi\Model;

use Dynamic\BrandsApi\Api\GetBrandList;

class GetBrandListModel implements GetBrandList
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Mage360\Brands\Model\Brands
     */
    protected $brands;

    /**
     * Brands data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mage360\Brands\Model\Brands $brands
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mage360\Brands\Model\Brands $brands,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->brands = $brands;
    }

    /**
     * Returns Brands data
     *
     * @api
     * @return return Brands array collection.
     */
    public function getList()
    {
        $data = [];

        $brandsCollection = $this->brands->getCollection()->addFieldToFilter('is_active', array('eq' => 1));

        if(count($brandsCollection) > 0 && !empty($brandsCollection)) {
            foreach ($brandsCollection as $brands) {
                $data[] = [
                    "brand_id" => $brands->getBrandId(),
                    "attribute_id" => $brands->getAttributeId(),
                    "name" => $brands->getName(),
                    "description" => $brands->getDescription(),
                    "url_key" => $brands->getUrlKey(),
                    "logo_path" => $brands->getLogoPath(),
                    "sort_order" => $brands->getSortOrder(),
                    "is_active" => $brands->getIsActive(),
                    "is_featured" => $brands->getIsFeatured(),
                    "seo_title" => $brands->getSeoTitle(),
                    "seo_desc" => $brands->getSeoDesc(),
                    "seo_keyword" => $brands->getSeoKeyword(),
                    "updated_at" => $brands->getUpdatedAt(),
                    "created_at" => $brands->getCreatedAt() 
                ];
            }
        } else {
            $data = array(
                ['status' => 'No Data','message' => __('There are no brands data in this website.') ]
            );
        }
        
        return $data;
    }
}
