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

namespace Bss\SocialLogin\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;

/**
 * Class DataHelp
 * @package Bss\SocialLogin\Helper
 */
class DataHelp extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var UrlFactory
     */
    protected $urlFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var FormFactory
     */
    protected $formFactory;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;
    /**
     * @var AddressInterfaceFactory
     */
    protected $addressDataFactory;
    /**
     * @var RegionInterfaceFactory
     */
    protected $regionDataFactory;
    /**
     * @var \Bss\SocialLogin\Model\Recaptcha
     */
    protected $recaptcha;

    /**
     * DataHelp constructor.
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param FormFactory $formFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param UrlFactory $urlFactory
     * @param \Bss\SocialLogin\Model\Recaptcha $recaptcha
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        FormFactory $formFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        UrlFactory $urlFactory,
        \Bss\SocialLogin\Model\Recaptcha $recaptcha
    ) {
        $this->recaptcha = $recaptcha;
        $this->addressDataFactory  = $addressDataFactory;
        $this->regionDataFactory   = $regionDataFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->formFactory = $formFactory;
        $this->urlFactory = $urlFactory;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        parent::__construct($context);
    }

    /**
     * @return \Bss\SocialLogin\Model\Recaptcha
     */
    public function getReCapcha()
    {
        return $this->recaptcha;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getSession()
    {
        return $this->customerSession;
    }

    /**
     * @return \Magento\Customer\Model\Url
     */
    public function getCustomerUrl()
    {
        return $this->customerUrl;
    }

    /**
     * @return UrlFactory
     */
    public function getUrlFactory()
    {
        return $this->urlFactory;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return SubscriberFactory
     */
    public function getSub()
    {
        return $this->subscriberFactory;
    }

    /**
     * @return AddressInterfaceFactory
     */
    public function getAddressFactory()
    {
        return $this->addressDataFactory;
    }

    /**
     * @return RegionInterfaceFactory
     */
    public function getRegionDataFactory()
    {
        return $this->regionDataFactory;
    }
}
