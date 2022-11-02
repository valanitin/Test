<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

use Magento\Framework\View\Element\Template\Context;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Data;
use Plumrocket\SocialLoginFree\Model\Config\Source\Position;

class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\SocialLoginFree\Helper\Data          $dataHelper
     * @param \Plumrocket\SocialLoginFree\Helper\Config        $config
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Config $config,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Get data helper
     *
     * @return \Plumrocket\SocialLoginFree\Helper\Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return bool
     */
    public function isModuleEnabledForRegistration(): bool
    {
        return $this->config->isModulePositionEnabled(Position::BUTTONS_POSITION_REGISTER);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
