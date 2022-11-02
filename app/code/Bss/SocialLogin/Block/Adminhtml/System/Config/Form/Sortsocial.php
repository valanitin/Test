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
 * Class Sortsocial
 *
 * @package Bss\SocialLogin\Block\Adminhtml\System\Config\Form
 */
class Sortsocial extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;

    /**
     * Sortsocial constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\SocialLogin\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/config/sortsocial.phtml');
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        
        return $this->toHtml();
    }

    /**
     * @return array|null
     */
    public function getButtons()
    {
        return $this->helper->getButtons();
    }

    /**
     * @return array
     */
    public function getPreparedButtons()
    {
        return $this->helper->getPreparedButtons();
    }
}
