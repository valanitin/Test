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
namespace Bss\SocialLogin\Block;

/**
 * Class SocialLogin
 * @package Bss\SocialLogin\Block
 */
class SocialLogin extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * SocialLogin constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\SocialLogin\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool
     */
    public function isEnabledPopup()
    {
        if (($this->request->getRouteName() !='customer')
            && ($this->request->getControllerName() !='account')
            && ($this->helper->popupEnabled())
            && ($this->helper->checkLogin())) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->helper->isSecure();
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/popup/login', ['_secure' => $this->isSecure()]);
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('sociallogin/popup/create', ['_secure' => $this->isSecure()]);
    }

    /**
     * @return string
     */
    public function getForgotUrl()
    {
        return $this->getUrl('sociallogin/popup/forgot', ['_secure' => $this->isSecure()]);
    }

    /**
     * @return bool
     */
    public function recaptchaLogin()
    {
        return $this->helper->recaptcha('login');
    }

    /**
     * @return bool
     */
    public function recaptchaForgot()
    {
        return $this->helper->recaptcha('fogot-pasw');
    }

    /**
     * @return bool
     */
    public function recaptchaCreate()
    {
        return $this->helper->recaptcha('create');
    }

    /**
     * @return array
     */
    public function getPreparedButtons()
    {
        return $this->helper->getPreparedButtons();
    }

    /**
     * @return bool
     */
    public function hasButtons()
    {
        return (bool)$this->helper->getPreparedButtons() && $this->helper->checkLogin();
    }

    /**
     * @return mixed
     */
    public function showLimit()
    {
        return $this->helper->showLimit();
    }

    /**
     * @return array
     */
    public function displayButtonLogin()
    {
        return $this->helper->displayButtonLogin();
    }

    /**
     * @return array
     */
    public function displayButtonRegister()
    {
        return $this->helper->displayButtonRegister();
    }

    /**
     * @return mixed
     */
    public function getSiteKey()
    {
        return $this->helper->getSiteKey();
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->helper->getTheme();
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->helper->getType();
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->helper->getSize();
    }
}
