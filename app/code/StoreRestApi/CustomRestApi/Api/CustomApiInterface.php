<?php
namespace StoreRestApi\CustomRestApi\Api;
/**
 * Interface CustomApiInterface
 *
 * @package Rk\CustomRestApi\Api
 */
interface CustomApiInterface
{
    /**
     * Get Customer List
     * @return string
     */
    public function getCustomerList();

    /**
     * Saving Website Data
     * @return string[]
    */
    
    public function saveWebsite(); 

    /**
     * Saving Website Data
     * @return string[]
    */
    
    public function editWebsite(); 	
}