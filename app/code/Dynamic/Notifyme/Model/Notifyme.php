<?php

namespace Dynamic\Notifyme\Model;

/**
 * Notifyme Model
 *
 * @method \Dynamic\Notifyme\Model\Resource\Page _getResource()
 * @method \Dynamic\Notifyme\Model\Resource\Page getResource()
 */
class Notifyme extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Notifyme\Model\ResourceModel\Notifyme');
    }

}
