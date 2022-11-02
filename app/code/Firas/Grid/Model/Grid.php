<?php

/**
 * Grid Grid Model.
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2017 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */
namespace Firas\Grid\Model;

use Firas\Grid\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'firas_ship_cost';

    /**
     * @var string
     */
    protected $_cacheTag = 'firas_ship_cost';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'firas_ship_cost';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Firas\Grid\Model\ResourceModel\Grid');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set EntityId.
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get ShippingCountryCode.
     *
     * @return varchar
     */
    public function getShippingCountryCode()
    {
        return $this->getData(self::SHIPPING_COUNTRY_CODE);
    }

    /**
     * Set ShippingCountryCode.
     */
    public function setShippingCountryCode($shippingCountryCode)
    {
        return $this->setData(self::SHIPPING_COUNTRY_CODE, $shippingCountryCode);
    }

    /**
     * Get ShippingCountryName.
     *
     * @return varchar
     */
    public function getShippingCountryName()
    {
        return $this->getData(self::SHIPPING_COUNTRY_NAME);
    }

    /**
     * Set ShippingCountryName.
     */
    public function setShippingCountryName($shippingCountryName)
    {
        return $this->setData(self::SHIPPING_COUNTRY_NAME, $shippingCountryName);
    }

    /**
     * Get ShippingPrice.
     *
     * @return int
     */
    public function getShippingPrice()
    {
        return $this->getData(self::SHIPPING_PRICE);
    }

    /**
     * Set ShippingPrice.
     */
    public function setShippingPrice($shippingPrice)
    {
        return $this->setData(self::SHIPPING_PRICE, $shippingPrice);
    }

    /**
     * Get ShippingPriceCurrency.
     *
     * @return varchar
     */
    public function getShippingPriceCurrency()
    {
        return $this->getData(self::SHIPPING_PRICE_CURRENCY);
    }

    /**
     * Set ShippingPriceCurrency.
     */
    public function setShippingPriceCurrency($shippingPriceCurrency)
    {
        return $this->setData(self::SHIPPING_PRICE_CURRENCY, $shippingPriceCurrency);
    }

    
}
