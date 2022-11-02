<?php

namespace Dynamic\Abandonedcartapi\Model;

/**
 * Abandonedcartapi Model
 *
 * @method \Dynamic\Abandonedcartapi\Model\Resource\Page _getResource()
 * @method \Dynamic\Abandonedcartapi\Model\Resource\Page getResource()
 */
class Abandonedcartapi extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi');
    }

}
