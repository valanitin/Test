<?php
namespace Zealousweb\AppleLogin\Block\System;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Redirect
 *
 * @package Mageplaza\SocialLogin\Block\System
 */
class RedirectUrl extends FormField
{
    /**
     * @type \Zealousweb\AppleLogin\Helper\Data
     */
    protected $helper;

    /**
     * RedirectUrl constructor.
     *
     * @param Context $context
     * @param \Zealousweb\AppleLogin\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Zealousweb\AppleLogin\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $redirectUrl = $this->helper->getRedirectUri();
        $html        = '<input style="opacity:1;" readonly id="apple-login" class="input-text admin__control-text" value="' . $redirectUrl . '" onclick="this.select()" type="text">';

        return $html;
    }
}
