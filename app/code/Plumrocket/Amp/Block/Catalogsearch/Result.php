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

class Result extends \Magento\CatalogSearch\Block\Result
{
    /**
     * Default template for block
     * @var string
     */
    protected $_template = 'Plumrocket_Amp::catalogsearch/result.phtml';

    /**
     * Rewrite parent method
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('amp_search_result_list')->getChildHtml('additional');
    }

    /**
     * Rewrite parent method
     * Retrieve search list toolbar block
     *
     * @return ListProduct
     */
    public function getListBlock()
    {
        return $this->getChildBlock('amp_search_result_list');
    }

    /**
     * Rewrite parent method
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('amp_search_result_list');
    }
}
