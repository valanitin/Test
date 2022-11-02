<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

/**
 * phpcs:disable
 */
class Callbackurl extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Provider\Callback
     */
    private $callback;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Plumrocket\SocialLoginFree\Model\Provider\Callback $callback
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\SocialLoginFree\Model\Provider\Callback $callback,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->callback        = $callback;
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $providerName = str_replace(['psloginfree_', '_callbackurl'], '', $element->getHtmlId());
        $url = $this->callback->getUrl($providerName, true);
        return '<input id="'. $element->getHtmlId() .'" type="text" name="" value="'. $url .'" class="input-text psloginfree-callbackurl-autofocus" style="background-color: #EEE; color: #999;" readonly="readonly" />';
    }
}
