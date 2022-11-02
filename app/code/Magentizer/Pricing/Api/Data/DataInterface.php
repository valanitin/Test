<?php
/*
* Magentizer_Pricing

* @category   SussexDev
* @package    Magentizer_Pricing
* @copyright  Copyright (c) 2019 Scott Parsons
* @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
* @version    1.1.2
*/
namespace Magentizer\Pricing\Api\Data;

interface DataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const PRICING_ID = 'pricing_id';
    const PRICING_SKU = 'pricing_sku';
    const PRICING_VALUE = 'pricing_value';
    const PRICING_STORE = 'pricing_store';
    const CREATED_AT = 'created_at';


    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param $id
     * @return DataInterface
     */
    public function setId($id);

    /**
     * Get Data Title
     *
     * @return string
     */
    public function getPricingSku();

    /**
     * Set Data Title
     *
     * @param $title
     * @return mixed
     */
    public function setPricingSku($sku);

    /**
     * Get Data Description
     *
     * @return mixed
     */
    public function getPricingValue();

    /**
     * Set Data Description
     *
     * @param $description
     * @return mixed
     */
    public function setPricingValue($price);

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getPricingStore();

    /**
     * Set is active
     *
     * @param $isActive
     * @return DataInterface
     */
    public function setPricingStore($pricingStore);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * set created at
     *
     * @param $createdAt
     * @return DataInterface
     */
    public function setCreatedAt($createdAt);
}
