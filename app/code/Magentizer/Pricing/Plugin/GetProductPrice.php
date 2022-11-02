<?php
 
namespace Magentizer\Pricing\Plugin;
 
class GetProductPrice
{
    public function __construct(
        \Magentizer\Pricing\Cron\FetchPrices $priceHelper
    ) {
        $this->priceHelper = $priceHelper;
    }
    
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $priceFromErp = $this->priceHelper->getProductPrice($subject->getSku()); 
        
        //$priceFromErp = 0;
        if($priceFromErp){
         return $priceFromErp;   
        }
        return $result;
    }
}