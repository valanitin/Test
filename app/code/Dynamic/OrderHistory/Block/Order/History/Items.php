<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 
namespace Dynamic\OrderHistory\Block\Order\History;

/**
 * Sales order view items block.
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Dynamic\OrderHistory\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Order items per page.
     *
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var \Dynamic\OrderHistory\Model\ResourceModel\OrderhistoryItem\CollectionFactory
     */
    private $itemCollectionFactory;
    

    /**
     * @var \Dynamic\OrderHistory\Model\ResourceModel\OrderhistoryItem\Collection|null
     */
    private $itemCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory|null $itemCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [],
        \Dynamic\OrderHistory\Model\ResourceModel\OrderhistoryItem\CollectionFactory $itemCollectionFactory = null
        
    ) {
        $this->_coreRegistry = $registry;
        $this->itemCollectionFactory = $itemCollectionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Dynamic\OrderHistory\Model\ResourceModel\OrderhistoryItem\CollectionFactory::class);
            
        parent::__construct($context, $data);
    }

    /**
     * Init pager block and item collection with page size and current page number
     *
     * @return $this
     * @since 100.1.7
     */
    protected function _prepareLayout()
    {
        $this->itemsPerPage = $this->_scopeConfig->getValue('sales/orders/items_per_page');

        $this->itemCollection = $this->itemCollectionFactory->create();
        //$this->itemCollection->setOrderFilter($this->getOrder());
        $this->itemCollection->addFieldToFilter('history_order_id', $this->getOrder()->getEntityId());

        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            //here pager updates collection parameters
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }

        return parent::_prepareLayout();
    }

    /**
     * Determine if the pager should be displayed for order items list.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return bool
     * @since 100.1.7
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * Get visible items for current page.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return \Magento\Framework\DataObject[]
     * @since 100.1.7
     */
    public function getItems()
    {
        return $this->itemCollection->getItems();
    }

    /**
     * Get pager HTML according to our requirements.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return string HTML output
     * @since 100.1.7
     */
    public function getPagerHtml()
    {
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        return $pagerBlock ? $pagerBlock->toHtml() : '';
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOriginalOrder()
    {
        return $this->_coreRegistry->registry('current_originalorder');
    }
    
    /**
     * Retrieve current order model instance
     *
     * @return \Dynamic\OrderHistory\Model\OrderHistory
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_historicalorder');
    }
}