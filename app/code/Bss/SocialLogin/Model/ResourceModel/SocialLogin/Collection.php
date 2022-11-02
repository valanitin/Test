<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SocialLogin\Model\ResourceModel\SocialLogin;

/**
 * Class Collection
 * @package Bss\SocialLogin\Model\ResourceModel\SocialLogin
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Bss\SocialLogin\Model\SocialLogin::class,
            \Bss\SocialLogin\Model\ResourceModel\SocialLogin::class
        );
    }

    /**
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['social_table' => $this->getTable('customer_entity')],
            'main_table.customer_id = social_table.entity_id',
            '*'
        );
        return $this;
    }
}
