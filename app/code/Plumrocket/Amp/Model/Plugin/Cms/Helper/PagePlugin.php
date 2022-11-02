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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Cms\Helper;

/**
 * Plugin for fix homepage
 */
class PagePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @return  void
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param  controller
     * @param  \Magento\Framework\App\Action\Action $action
     * @param  string                               $pageId
     * @return array
     */
    public function beforePrepareResultPage($subject, $action, $pageId = null)
    {
        /**
         * Set PageId amp-homepage
         * (Only for amp request)
         */
        if ($this->dataHelper->isAmpRequest() && 'cms_index_index' == $this->dataHelper->getFullActionName()) {
            $pageId = \Plumrocket\Amp\Helper\Data::AMP_HOME_PAGE_KEYWORD;
        }

        return [$action, $pageId];
    }
}
