<?php

namespace Zealousweb\AppleLogin\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;

class AdditionalConfigVars implements ConfigProviderInterface
{
	/** 
	 * @var \Zealousweb\AppleLogin\Helper\Data
	 */
    protected $helper;

    /** 
     * @var \Zealousweb\AppleLogin\Model\Config\Source\DisplayType
     */
    protected $displayType;

    public function __construct(
        \Zealousweb\AppleLogin\Helper\Data $helper,
        \Zealousweb\AppleLogin\Model\Config\Source\DisplayType $displayType
    ) {
        $this->helper = $helper;
        $this->displayType = $displayType;
    }

    /**
     * Return config value;
     * @return mixed
     */
	public function getConfig()
	{
        $defaultImage = $this->helper->getDefaultImage();

		$additionalVariables['authorizationUrl'] = $this->helper->getAuthorizationUrl();
        $additionalVariables['isActive'] = ($this->helper->isEnabled()) ? $this->helper->isEnabled(): false;
        $additionalVariables['buttonLabel'] = $this->helper->getButtonLabel();
        $additionalVariables['buttonLayout'] = 'apple-icon-'.$this->helper->getButtonLayout();
        $additionalVariables['appleButtonClass'] = 'apple-btn-'.$this->helper->getButtonLayout();
        $additionalVariables['backgroundColor'] = $this->helper->getButtonBackgroundColor(); 
        $additionalVariables['displayType'] = $this->helper->getDisplayType();
        $additionalVariables['buttonImage'] = (!empty($this->helper->getButtonImage())) ? $this->helper->getResizeImage('apple/'.$this->helper->getButtonImage(), 22, 27) : $defaultImage;
        $additionalVariables['appleIcon'] = (!empty($this->helper->getAppleIcon())) ? $this->helper->getResizeImage('apple/'.$this->helper->getAppleIcon(), 90, 90) : $defaultImage;
        $additionalVariables['is_show_button_on_checkout'] = ($this->helper->isShowButtonOnCheckout()) ? $this->helper->isShowButtonOnCheckout(): false;
        $additionalVariables['is_show_button_on_cart'] = ($this->helper->isShowButtonOnCart()) ? $this->helper->isShowButtonOnCart(): false;
        
		return $additionalVariables;
	}

    /**
     * Get display type options array
     * @return array
     */
    public function getDisplayTypeArray()
    {
        return $this->displayType->toArray();
    }
}