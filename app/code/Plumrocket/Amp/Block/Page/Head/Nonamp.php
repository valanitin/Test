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

namespace Plumrocket\Amp\Block\Page\Head;

class Nonamp extends \Magento\Framework\View\Element\Template
{
    /**
     * Default template for block
     *
     * @var string
     */
    protected $_template = 'Plumrocket_Amp::head/nonamp.phtml';

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Plumrocket\Amp\Helper\Data            $dataHelper
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve amp url of current page
     *
     * @return string
     */
    public function getAmpUrl() : string
    {
        return $this->dataHelper->getHtmlAmpUrl();
    }

    /**
     * Only for not amp pages
     *
     * @return string
     */
    protected function _toHtml() : string //@codingStandardsIgnoreLine
    {
        if ($this->dataHelper->isAmpRequest()) {
            return '';
        }

        return parent::_toHtml();
    }
}
