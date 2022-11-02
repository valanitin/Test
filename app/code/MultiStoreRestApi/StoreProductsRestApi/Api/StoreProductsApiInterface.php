<?php
namespace MultiStoreRestApi\StoreProductsRestApi\Api;
/**
 * Interface CustomApiInterface
 *
 * @package Rk\CustomRestApi\Api
 */
interface StoreProductsApiInterface
{
    /**
     * Saving Website Data
     * @return string[]
    */
    
    public function UpdateStoreProducts(); 



      /**
     * Saving Website Data
     * @return string[]
    */

    public function UpdateProductprice();

	/**
     * Update Product Qty
     * @return string[]
     */
    public function UpdateProductQty();


}