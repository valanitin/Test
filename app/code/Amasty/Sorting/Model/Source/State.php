<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

/**
 * Class State
 *
 * @package Amasty\Sorting\Model\Source
 */
class State implements \Magento\Framework\Option\ArrayInterface
{
    private $options;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var \Magento\Sales\Model\Order\Status[] $statusItems */
            $statusItems = $this->statusCollectionFactory->create()->getItems();

            foreach ($statusItems as $status) {
                $this->options[] = ['value' => $status->getStatus(), 'label' => $status->getLabel()];
            }
        }

        return $this->options;
    }
}
