<?php
/**
 * Firas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please contact us https://firas.co.uk/contacts.
 *
 * @category   Firas
 * @package    Firas_AdminActivity
 * @copyright  Copyright (C) 2018 Kiwi Commerce Ltd (https://firas.co.uk/)
 * @license    https://firas.co.uk/magento2-extension-license/
 */
namespace Firas\AdminActivity\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Activity
 * @package Firas\Activity\Model
 */
class Activity extends AbstractModel
{
    /**
     * @var string
     */
    const ACTIVITY_ID = 'entity_id'; // We define the id field name

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Firas\AdminActivity\Model\ResourceModel\Activity');
    }
}
