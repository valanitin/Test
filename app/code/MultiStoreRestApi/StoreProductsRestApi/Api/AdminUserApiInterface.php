<?php
namespace MultiStoreRestApi\StoreProductsRestApi\Api;
/**
 * Interface AdminUserApiInterface
 *
 * @package Rk\CustomRestApi\Api
 */
interface AdminUserApiInterface
{
    /**
     * Saving Website Data
     * @return string[]
    */
    public function createAdminUser();
	
	/**
     * Delete Admin user
     * @return string[]
    */
    public function deleteAdminUser();
	
	/**
     * Delete Admin user
     * @return string[]
    */
    public function editAdminUser();
}