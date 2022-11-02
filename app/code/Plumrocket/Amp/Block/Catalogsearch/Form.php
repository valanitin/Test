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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalogsearch;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Default template for block
     * @var string
     */
    protected $_template = 'Plumrocket_Amp::catalogsearch/form.phtml';

    /**
     * Return url action for search form
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->_urlBuilder->getUrl("catalogsearch/result/index", ['_secure' => true]);
    }
}
