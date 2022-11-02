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
namespace Bss\SocialLogin\Model\ResourceModel;

/**
 * Class GetCustomer
 * @package Bss\SocialLogin\Model\ResourceModel
 */
class GetCustomer
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $connection;

    /**
     * GetCustomer constructor.
     * @param \Magento\Framework\App\ResourceConnection $connection
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @param $id
     * @return string
     */
    public function getCusId($id)
    {
        $connection = $this->connection->getConnection();
        try {
            $select=$connection
                ->select()
                ->from(['main_table' =>$this->connection->getTableName('bss_sociallogin')],
                    ['password_fake_email'] )
                ->where('customer_id = ?', $id);
            $passWordFakeEmail=$connection->fetchOne($select);
    } catch (\Magento\Framework\Model\Exception $e) {
            $passWordFakeEmail="";
        }
        return $passWordFakeEmail;
    }
}
