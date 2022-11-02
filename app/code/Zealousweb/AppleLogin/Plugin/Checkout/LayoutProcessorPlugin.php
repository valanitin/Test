<?php
namespace Zealousweb\AppleLogin\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class LayoutProcessorPlugin
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface 
     */
    protected $productMetaData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Framework\App\ProductMetadataInterface 
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->productMetaData = $productMetaData;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout) {

        $version = $this->productMetaData->getVersion();

        if (version_compare($version, '2.3.4', '>') && $this->isAmazonPayEnable()) {
            $jsLayout = $this->updateLayoutWithAmazon($jsLayout);
        }
        else{
            $jsLayout = $this->updateLayoutWithoutAmazon($jsLayout);
        }

        return $jsLayout;
    }

    /**
     * Check amazon module is enable or not
     *
     * @return bool
     */
    private function isAmazonPayEnable(){

        return $this->moduleManager->isOutputEnabled('Amazon_Payment');
    }

    /**
     * Update layout for Magento version higher then 2.3.5
     * This will use the Amazon pay module js
     *
     * @param array $jsLayout
     */
    private function updateLayoutWithAmazon($jsLayout){

        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['component'])){
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['component'] = 'Zealousweb_AppleLogin/js/view/form/element/email_amzn';    
        }
        
        return $jsLayout;
    }

    /**
     * Update layout for Magento version lower then 2.3.5
     * This will not use the Amazon pay module js
     *
     * @param array $jsLayout
     */
    private function updateLayoutWithoutAmazon($jsLayout){

        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['component'])){
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['component'] = 'Zealousweb_AppleLogin/js/view/form/element/email';    
        }
        
        return $jsLayout;
    }
}