<?php

namespace Dynamic\Orderreturn\Model;

/**
 * Orderreturn Model
 *
 * @method \Dynamic\Orderreturn\Model\Resource\Page _getResource()
 * @method \Dynamic\Orderreturn\Model\Resource\Page getResource()
 */
class Orderreturn extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Orderreturn\Model\ResourceModel\Orderreturn');
    }

}
