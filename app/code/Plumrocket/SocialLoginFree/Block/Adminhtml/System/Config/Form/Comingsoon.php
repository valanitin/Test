<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

class Comingsoon extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<div style="padding:10px;background-color:#fff;color:#d83820;border:1px solid #ddd;margin-bottom:7px;">'
            . __('This network is coming soon. It will be available via free update.')
            . '</div>';
    }
}
