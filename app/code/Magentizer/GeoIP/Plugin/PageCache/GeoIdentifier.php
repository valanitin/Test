<?php
/**
 * Copyright © 2016 ToBai. All rights reserved.
 */
namespace Magentizer\GeoIP\Plugin\PageCache;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class GeoIdentifier
{
   protected $_cookieManager;
public function __construct(
     \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
)
{
     $this->_cookieManager = $cookieManager;
}
    
    public function afterGetValue(\Magento\Framework\App\PageCache\Identifier $identifier, $result)
    {
        $currentMbStoreCookie = $this->_cookieManager->getCookie("mb_store_cookie");
        if(!isset($currentMbStoreCookie)){
           
        $result .=$currentMbStoreCookie;
        }
        return $result;
    }
}
