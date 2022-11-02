<?php
namespace Zealousweb\AppleLogin\Block\System;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Redirect
 *
 * @package Mageplaza\SocialLogin\Block\System
 */
class Date extends FormField
{
    protected $_coreRegistry;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $coreRegistry, 
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
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
        $html = $element->getElementHtml();
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }
        $html .= '<button type="button" class="custom-ui-datepicker-trigger ui-datepicker-trigger v-middle"><span>Select Date</span></button>';
        
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "dd/mm/yy" } );
                    jq("#'.$element->getHtmlId() .'" ).addClass("_has-datepicker");
                    jq(".custom-ui-datepicker-trigger").removeAttr("style");
                    jq(".custom-ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';



        return $html;
    }
}
