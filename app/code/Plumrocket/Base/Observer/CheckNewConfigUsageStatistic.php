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
 * Change status of config statistic for specific module after save config
 *
 * @since 2.3.0
 */
class CheckNewConfigUsageStatistic implements ObserverInterface
{
    /**
     * @var \Plumrocket\Base\Model\Statistic\Usage\StatusInterface[]
     */
    private $statuses;

    /**
     * CheckNewConfigStatistic constructor.
     *
     * @param \Plumrocket\Base\Model\Statistic\Usage\StatusInterface[] $statuses
     */
    public function __construct(array $statuses = [])
    {
        $this->statuses = $statuses;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $section = $observer->getRequest()->getParam('section', '');

        if (isset($this->statuses[$section])) {
            $this->statuses[$section]->switchToCollect();
        }
    }
}
