<?php

namespace Dynamic\Notifyme\Model\ResourceModel;

/**
 * Notifyme Resource Model
 */
class Notifyme extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('notifyme', 'notifyme_id');
    }
}
