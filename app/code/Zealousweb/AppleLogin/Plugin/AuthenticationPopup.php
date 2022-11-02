<?php

namespace Zealousweb\AppleLogin\Plugin;

class AuthenticationPopup
{
	/** 
	 * @var \Zealousweb\AppleLogin\Helper\Data
	 */
    protected $helper;

    /** 
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
    protected $storeManager;

    public function __construct(
        \Zealousweb\AppleLogin\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

	public function aroundGetConfig(\Magento\Customer\Block\Account\AuthenticationPopup $subject, callable $proceed)
	{
		$result = $proceed();		
		$result['authorizationUrl'] = $this->helper->getAuthorizationUrl();
		$result['redirectionUrl'] = $this->storeManager->getStore()->getBaseUrl().'/applelogin/apple/redirect';
		$result['isActive'] = ($this->helper->isEnabled()) ? $this->helper->isEnabled(): false;

		return $result;
	}

}