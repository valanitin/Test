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
 * @package     Plumrocket Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Search;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $_dataHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Rendering Search Page
     *
     * @param void
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute()
    {
        /**
         * Checking AMP search status
         */
        if (!$this->_dataHelper->isSearchEnabled()) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('noRoute');

            return $resultRedirect;
        }

        /**
         * If the request isn't AMP
         */
        if (!$this->_dataHelper->isAmpRequest()) {
            $this->_response->setRedirect($this->_dataHelper->getAmpUrl(), 301)->sendResponse();
        }

        $this->_view->loadLayout();

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Search'));

        $this->_view->renderLayout();
    }
}
