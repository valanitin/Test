<?php

namespace Dynamic\Orderreturn\Model;

use Dynamic\Orderreturn\Api\GetReturnStatus;

class GetReturnStatusModel implements GetReturnStatus
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
     * Returns return status data
     *
     * @api
     * @return return return status array collection.
     */
    public function getReturnStatusList()
    {
        $data = [];
            
        $systemConfig = $this->jsonHelper->jsonDecode($this->getConfig(), true);
        if(!empty($systemConfig)) {
            foreach ($systemConfig as $reason) {
                $data[] = $reason['reason_data'];
            }

        } else {
            $data = array(
                ['status' => 'No Data','message' => __('There are no Reason data in this website.') ]
            );
        }
        
        return $data;
    }

    public function getConfig()
    {
        return $this->scopeConfig->getValue('orderreturn_reason/orderreturn_configuration/reason', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
