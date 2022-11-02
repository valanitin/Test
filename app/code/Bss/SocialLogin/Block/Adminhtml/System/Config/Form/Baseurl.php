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
namespace Bss\SocialLogin\Block\Adminhtml\System\Config\Form;

/**
 * Class Baseurl
 *
 * @package Bss\SocialLogin\Block\Adminhtml\System\Config\Form
 */
class Baseurl extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Baseurl constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        return '<input id="' . $element->getHtmlId() . '" type="text" name="" value="' .
            $this->_storeManager->getStore()->getBaseUrl() . '" class="input-text sociallogin-callbackurl-autofocus" 
            style="background-color: #EEE; color: #999;" readonly="readonly" />';
    }
}
