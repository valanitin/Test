<?php

namespace Dynamic\Cmspagemanager\Model;

/**
 * Cmspagemanager Model
 *
 * @method \Dynamic\Cmspagemanager\Model\Resource\Page _getResource()
 * @method \Dynamic\Cmspagemanager\Model\Resource\Page getResource()
 */
class Cmspagemanager extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager');
    }

}
