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
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page;

/**
 * Class Flag
 *
 * @method string|null getClass()
 * @method string|null getCode()
 * @method array|null  getMapping()
 *
 * @method $this setClass($class)
 * @method $this setCode($code)
 */
class Flag extends \Magento\Framework\View\Element\Template
{
    /**
     * Set default template
     *
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _beforeToHtml() : \Magento\Framework\View\Element\Template //@codingStandardsIgnoreLine
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::page/flag.phtml');
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    public function getImageUrl() : string
    {
        if (! $this->getCode()) {
            $this->_logger->debug('Code not set');
            return '';
        }

        return $this->getViewFileUrl(sprintf($this->getPathTemplate(), $this->getFlagFileName($this->getCode())));
    }

    /**
     * @return string
     */
    public function getPathTemplate() : string
    {
        return 'Plumrocket_Amp::images/flags/%s.svg';
    }

    /**
     * @return array
     */
    public function getDefaultMapping() : array
    {
        return [
            'fr_CA' => 'en_CA',
            'gl_ES' => 'ca_ES',
            'hi_IN' => 'gu_IN',
            'it_CH' => 'de_CH',
            'cy_GB' => 'en_GB',
        ];
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getFlagFileName($code) : string
    {
        $mapping = $this->getMapping() ?: $this->getDefaultMapping();

        return $mapping[$code] ?? $code;
    }

    /**
     * @return string
     */
    public function render() : string
    {
        return $this->toHtml();
    }
}
