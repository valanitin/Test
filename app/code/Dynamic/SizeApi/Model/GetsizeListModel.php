<?php

namespace Dynamic\SizeApi\Model;

use Dynamic\SizeApi\Api\Getsize;

class GetsizeListModel implements Getsize
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Size data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Returns size data
     *
     * @api
     * @return return size array collection.
     */
    public function getSizeList($categoryId)
    {
        $data = [];
            
        $systemConfig = $this->getConfig($categoryId);
        if($systemConfig) {
            $sizeCollection = explode(",", $systemConfig);
            if(count($sizeCollection) > 0 && !empty($sizeCollection)) {
                foreach ($sizeCollection as $size) {
                    $data[] = $size;
                }
            } else {
                $data = array(
                    ['status' => 'No Data','message' => __('There are no Size data in this website.') ]
                );
            }
        } else {
            $data = array(
                ['status' => 'No Data','message' => __('There are no Size data in this website.') ]
            );
        }
        
        return $data;
    }

    public function getConfig($categoryId)
    {
        return $this->scopeConfig->getValue('sizeapi/sizevalue/'.$categoryId, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
