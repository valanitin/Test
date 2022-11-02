<?php
namespace Local\ProductOutofstock\Cron;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class ProductQty
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * @var  \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @var  \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $_productAction;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var  \Local\ProductOutofstock\Model\OutofstockFactory
     */
    protected $_outofstock;

    /**
     * @var  \Local\ProductOutofstock\Helper\Data
     */
    protected $_helperConfig;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $productAction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Local\ProductOutofstock\Model\OutofstockFactory $outofstock
     * @param \Local\ProductOutofstock\Helper\Data $helperConfig
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Local\ProductOutofstock\Model\OutofstockFactory $outofstock,
        \Local\ProductOutofstock\Helper\Data $helperConfig
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_productAction = $productAction;
        $this->_storeManager = $storeManager;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_outofstock = $outofstock;
        $this->_helperConfig = $helperConfig;
    }

    /**
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/outofstockCron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Enable: ' . $this->_helperConfig->isEnableModule());

        if ($this->_helperConfig->isEnableModule()) {
            $disabledDays = ($this->_helperConfig->getDays() == '' ? $this->_helperConfig->getDays() : 30);
            $collection = $this->_productCollectionFactory->create();
            $collection->setFlag('has_stock_status_filter', true);
            $collectionSimple = $collection->addAttributeToSelect(['entity_id'])
                ->addAttributeToSort('created_at', 'DESC')
                ->joinField(
                    'qty',
                    'cataloginventory_stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left'
                )->joinTable('cataloginventory_stock_item', 'product_id=entity_id', ['stock_status' => 'is_in_stock'])
                ->addAttributeToSelect('stock_status')
                ->addFieldToFilter('stock_status', ['eq' => 0])
                ->load();

            $collectionConfigure = $this->_productCollectionFactory->create();
            $collectionConfigure->setFlag('has_stock_status_filter', true);
            $collectionConfigure = $collectionConfigure->addAttributeToSelect(['entity_id'])->joinField(
                'links',
                'catalog_product_super_link',
                'product_id',
                'parent_id=entity_id',
                null,
                'left'
            )->joinField(
                'item',
                'cataloginventory_stock_item',
                '*',
                'product_id=links',
                '{{table}}.qty = 0',
                'inner'
            )->groupByAttribute('entity_id');

            $model = $this->_outofstock->create();
            $data = [];
            $i = 0;
            foreach ($collectionSimple as $product) {
                $collection = $model->getCollection()->addFieldToFilter('product_id', $product['entity_id']);
                if (count($collection)) {
                    $savedDate = $collection->getData()[0]['outofstock_date'];
                    $currentDate = date_create($this->_timezoneInterface->date()->format('Y-m-d H:i:s'));
                    $updateData = date_create($savedDate);
                    $interval = date_diff($updateData, $currentDate); /* @phpstan-ignore-line */
                    $totalDiff = (int) $interval->days;
                    if ($totalDiff >= $disabledDays) {
                        $id = $collection->getData()[0]['id'];
                        $savedProductId = $collection->getData()[0]['product_id'];
                        $this->changeProductStatus($savedProductId);
                        $model->load($id);
                        $model->delete();
                    }
                } else {
                    $data[$i]['product_id'] = $product['entity_id'];
                    $data[$i++]['outofstock_date'] = date("Y/m/d");
                }
            }

            foreach ($collectionConfigure as $product) {
                $collection = $model->getCollection()->addFieldToFilter('product_id', $product['entity_id']);
                if (count($collection)) {
                    $savedDate = $collection->getData()[0]['outofstock_date'];
                    $currentDate = date_create($this->_timezoneInterface->date()->format('Y-m-d H:i:s'));
                    $updateData = date_create($savedDate);
                    $interval = date_diff($updateData, $currentDate); /* @phpstan-ignore-line */
                    $totalDiff = (int) $interval->days;
                    if ($totalDiff >= $disabledDays) {
                        $id = $collection->getData()[0]['id'];
                        $savedProductId = $collection->getData()[0]['product_id'];
                        $this->changeProductStatus($savedProductId);
                        $model->load($id);
                        $model->delete();
                    }
                } else {
                    $data[$i]['product_id'] = $product['entity_id'];
                    $data[$i++]['outofstock_date'] = date("Y/m/d");
                }
            }

            if (!empty($data)) {
                foreach ($data as $row) {
                    $model->setData($row);
                    $model->save();
                }
            }
        }
    }

    /**
     * @param int $productId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function changeProductStatus($productId)
    {
        $productIds = [$productId];
        $attributes = [
            'status' => Status::STATUS_ENABLED
        ];
        $storeCollection = $this->_storeManager->getStores();
        foreach ($storeCollection as $s) {
            $this->_productAction->updateAttributes(
                $productIds,
                $attributes,
                $s->getId()
            );
        }
        $this->_productAction->updateAttributes($productIds, $attributes, 0);
    }
}
