<?php

namespace Meetanshi\DeferJS\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Meetanshi\DeferJS\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $active = $this->scopeConfig->getValue('deferjs/general/active', ScopeInterface::SCOPE_STORE);
        if ($active) {
            return true;
        }
        return false;
    }
}
