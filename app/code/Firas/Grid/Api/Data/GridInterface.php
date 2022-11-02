<?php
/**
 * Grid GridInterface.
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2017 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */

namespace Firas\Grid\Api\Data;

interface GridInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'entity_id';
    const SHIPPING_COUNTRY_CODE = 'shipping_country_code';
    const SHIPPING_COUNTRY_NAME = 'shipping_country_name';
    const SHIPPING_PRICE = 'shipping_price';
    const SHIPPING_PRICE_CURRENCY = 'shipping_price_currency';
    

   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getEntityId();

   /**
    * Set EntityId.
    */
    public function setEntityId($entityId);

   /**
    * Get ShippingCountryCode.
    *
    * @return varchar
    */
    public function getShippingCountryCode();

   /**
    * Set ShippingCountryCode.
    */
    public function setShippingCountryCode($shippingCountryCode);

   /**
    * Get ShippingCountryName.
    *
    * @return varchar
    */
    public function getShippingCountryName();

   /**
    * Set ShippingCountryName.
    */
    public function setShippingCountryName($shippingCountryName);

    /**
    * Get ShippingPrice.
    *
    * @return int
    */
    public function getShippingPrice();

   /**
    * Set ShippingPrice.
    */
    public function setShippingPrice($shippingPrice);

   /**
    * Get ShippingPriceCurrency.
    *
    * @return varchar
    */
    public function getShippingPriceCurrency();

   /**
    * Set ShippingPriceCurrency.
    */
    public function setShippingPriceCurrency($shippingPriceCurrency);

   
}
