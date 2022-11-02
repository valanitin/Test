<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Base observer
 */
class PredispathAdminActionControllerObserver implements ObserverInterface
{
    /**
     * @var \Plumrocket\Base\Model\AdminNotificationFeedFactory
     */
    protected $_feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Plumrocket\Base\Model\AdminNotificationFeedFactory $feedFactory
     * @param \Magento\Backend\Model\Auth\Session                 $backendAuthSession
     */

    public function __construct(
        \Plumrocket\Base\Model\AdminNotificationFeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * Predispath admin action controller
     *
     * @param                                         \Magento\Framework\Event\Observer $observer
     * @return                                        void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->_feedFactory->create();
            /* @var \Plumrocket\Base\Model\AdminNotificationFeed $feedModel */
            $feedModel->checkUpdate();
        }
    }
}
