<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface;

class Sortable extends Field
{
    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public $element;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface
     */
    private $buttonProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context                   $context
     * @param \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface $buttonProvider
     * @param array                                                     $data
     */
    public function __construct(
        Context $context,
        ProviderInterface $buttonProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->buttonProvider = $buttonProvider;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/config/sortable.phtml');
    }

    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    public function getButtons(): array
    {
        return $this->buttonProvider->getPreparedButtons(true, false);
    }
}
