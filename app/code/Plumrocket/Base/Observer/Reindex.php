<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Base observer
 */
class Reindex implements ObserverInterface
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Reindex
     */
    private $reindex;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Reindex $reindex
     */
    public function __construct(\Plumrocket\Base\Model\Extension\Authorization\Reindex $reindex)
    {
        $this->reindex = $reindex;
    }

    /**
     * Predispath admin action controller
     *
     * @param                                         \Magento\Framework\Event\Observer $observer
     * @return                                        void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->reindex->execute();
    }
}
