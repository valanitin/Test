<?php
/**
 * Copyright © 2016 ToBai. All rights reserved.
 */
namespace Magentizer\GeoIP\Plugin;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class StoreResolver
{
protected $_cookieManager;
public function __construct(
     \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
)
{
     $this->_cookieManager = $cookieManager;
}
    /**
     * @param \Magento\Store\Api\StoreResolverInterface $subject
     * @param int|string $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCurrentStoreId(
        \Magento\Store\Api\StoreResolverInterface $subject,
        $result
    ) {
        
        $currentMbStoreCookie = $this->_cookieManager->getCookie("mb_store_cookie");
        if(!isset($currentMbStoreCookie)){
          
          return $currentMbStoreCookie ?: $result;  
        }else{
           return $result; 
        }
        
        
    }
}
