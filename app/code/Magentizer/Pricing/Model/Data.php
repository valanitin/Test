<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Model;

use Magento\Framework\Model\AbstractModel;
use Magentizer\Pricing\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'magentizer_erp_pricing';

    /**
     * Initialise resource model
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Magentizer\Pricing\Model\ResourceModel\Data');
    }

    /**
     * Get cache identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getPricingSku()
    {
        return $this->getData(DataInterface::PRICING_SKU);
    }

    /**
     * Set title
     *
     * @param $title
     * @return $this
     */
    public function setPricingSku($sku)
    {
        return $this->setData(DataInterface::PRICING_SKU, $sku);
    }

    /**
     * Get data description
     *
     * @return string
     */
    public function getPricingValue()
    {
        return $this->getData(DataInterface::PRICING_VALUE);
    }

    /**
     * Set data description
     *
     * @param $description
     * @return $this
     */
    public function setPricingValue($price)
    {
        return $this->setData(DataInterface::PRICING_VALUE, $price);
    }

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getPricingStore()
    {
        return $this->getData(DataInterface::PRICING_STORE);
    }

    /**
     * Set is active
     *
     * @param $isActive
     * @return $this
     */
    public function setPricingStore($store)
    {
        return $this->setData(DataInterface::PRICING_STORE, $store);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(DataInterface::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(DataInterface::CREATED_AT, $createdAt);
    }

}
