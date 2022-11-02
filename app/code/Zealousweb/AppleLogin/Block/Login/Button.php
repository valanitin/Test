<?php

namespace Zealousweb\AppleLogin\Block\Login;

class Button extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}