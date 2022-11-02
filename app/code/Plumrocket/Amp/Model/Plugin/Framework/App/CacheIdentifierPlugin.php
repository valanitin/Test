<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\App;

/**
 * CacheIdentifier plugin
 */
class CacheIdentifierPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $_config;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_config = $config;
    }

    /**
     * Adds a amp key to identifier for a built-in cache if is force to amp enabled
     *
     * @param \Magento\Framework\App\PageCache\Identifier $identifier
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetValue(\Magento\Framework\App\PageCache\Identifier $identifier, $result)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return $result;
        }

        if ($this->_config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->_config->isEnabled()) {
            $forceOnMobile = $this->_dataHelper->forceOnMobile();
            if ($forceOnMobile) {
                $forceOnTablet = $this->_dataHelper->forceOnTablet();
                $isMobile = $this->_dataHelper->isMobile();
                $isTablet = $this->_dataHelper->isTablet();
                if ($isMobile && !$isTablet) {
                    return $result . 'a' ;
                } elseif ($forceOnTablet && $isTablet) {
                    return $result . 'a' ;
                }
            }
        }

        return $result;
    }
}
