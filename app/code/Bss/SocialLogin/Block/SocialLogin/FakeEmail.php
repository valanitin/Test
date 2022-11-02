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

namespace Bss\SocialLogin\Block\SocialLogin;

use Bss\SocialLogin\Helper\Data;
use Bss\SocialLogin\Model\ResourceModel\GetCustomer;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

/**
 * Class FakeEmail
 *
 * @package Bss\SocialLogin\Block\SocialLogin
 */
class FakeEmail extends Template
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Customer
     */
    protected $customer;
    /**
     * @var GetCustomer
     */
    protected $select;
    /**
     * @var ResourceConnection
     */
    protected $connection;

    /**
     * FakeEmail constructor.
     * @param Template\Context $context
     * @param GetCustomer $select
     * @param ResourceConnection $connection
     * @param Session $session
     * @param Data $helper
     * @param Customer $customer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        GetCustomer $select,
        ResourceConnection $connection,
        Session $session,
        Data $helper,
        Customer $customer,
        array $data = []
    ) {
        $this->select = $select;
        $this->connection = $connection;
        $this->customer = $customer;
        $this->session = $session;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return (bool)$this->helper->moduleEnabled();
    }

    /**
     * @return bool
     */
    public function isFakeEmail()
    {
        $id = $this->session->getCustomerId();
        $email = $this->customer->load($id)->getEmail();
        $types = ['@facebook-user.com', '@googleplus-user.com', '@instagram-user.com', '@linkedin-user.com',
            '@live-user.com', '@twitter-user.com', '@vkontakte-user.com', '@yahoo-user.com', '@pinterest-user.com'];
        $fake_email = false;
        foreach ($types as $type) {
            $fakeEmail = strpos($email, $type);
            if ($fakeEmail) {
                $fake_email = true;
                break;
            }
        }
       return (bool)$fake_email;
    }

    /**
     * Get pass Fake Email
     *
     * @return string
     */
    public function getPass()
    {
        $id = $this->session->getCustomerId();
        return $this->select->getCusId($id);
    }

}
