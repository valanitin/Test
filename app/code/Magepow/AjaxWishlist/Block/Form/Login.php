<?php
/**
 * 
 * @category: Magepow
 * @Copyright (c) 2014 Magepow  (<https://www.magepow.com>)
 * @authors: Magepow (<magepow<support@magepow.com>>)
 * @date:    2021-04-26 09:16:20
 * @license: <http://www.magepow.com/license-agreement>
 * @github: <https://github.com/magepow> 
 */
namespace Magepow\AjaxWishlist\Block\Form;

class Login extends \Magento\Framework\View\Element\Template {
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Registration
     *
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Registration $registration
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->registration = $registration;
        $this->_logo = $logo;
    }

    /**
     * Return registration
     *
     * @return \Magento\Customer\Model\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }
     /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {    
        return $this->_logo->getLogoSrc();
    }
    

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('customer/ajax/login');
    }

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return (bool)!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerIsAlreadyLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Retrieve registering URL
     *
     * @return string
     */
    public function getCustomerRegistrationUrl()
    {
        return $this->getUrl('customer/account/create');
    }
}