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
namespace Bss\SocialLogin\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Bss\SocialLogin\Helper\Data;

/**
 * Class Customer
 * @package Bss\SocialLogin\CustomerData
 */
class Customer implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Customer constructor.
     * @param CurrentCustomer $currentCustomer
     * @param Data $helper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Data $helper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->helper = $helper;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSectionData()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return [
            'photo' => $customerId ? $this->helper->getPhotoPath() : '',
        ];
    }
}
