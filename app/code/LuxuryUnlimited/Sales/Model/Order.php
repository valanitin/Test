<?php

namespace LuxuryUnlimited\Sales\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order\ProductOption;

class Order extends \Magento\Sales\Model\Order
{
    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $item;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ResolverInterface|null $localeResolver
     * @param ProductOption|null $productOption
     * @param OrderItemRepositoryInterface|null $itemRepository
     * @param SearchCriteriaBuilder|null $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\Model\Context                                          $context,
        \Magento\Framework\Registry                                               $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory                         $extensionFactory,
        AttributeValueFactory                                                     $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone,
        \Magento\Store\Model\StoreManagerInterface                                $storeManager,
        \Magento\Sales\Model\Order\Config                                         $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface                           $productRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory           $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility                                 $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface                             $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory                                  $currencyFactory,
        \Magento\Eav\Model\Config                                                 $eavConfig,
        \Magento\Sales\Model\Order\Status\HistoryFactory                          $orderHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory        $addressCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory        $paymentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory        $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory       $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory     $memoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory                $salesOrderCollectionFactory,
        PriceCurrencyInterface                                                    $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory            $productListFactory,
        \Magento\Framework\App\ResourceConnection                                 $resourceConnection,
        \Magento\Sales\Model\Order\Item                                           $item,
        \Magento\Framework\Model\ResourceModel\AbstractResource                   $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb                             $resourceCollection = null,
        array                                                                     $data = [],
        ResolverInterface                                                         $localeResolver = null,
        ProductOption                                                             $productOption = null,
        OrderItemRepositoryInterface                                              $itemRepository = null,
        SearchCriteriaBuilder                                                     $searchCriteriaBuilder = null
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->item = $item;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $timezone, $storeManager, $orderConfig, $productRepository, $orderItemCollectionFactory, $productVisibility, $invoiceManagement, $currencyFactory, $eavConfig, $orderHistoryFactory, $addressCollectionFactory, $paymentCollectionFactory, $historyCollectionFactory, $invoiceCollectionFactory, $shipmentCollectionFactory, $memoCollectionFactory, $trackCollectionFactory, $salesOrderCollectionFactory, $priceCurrency, $productListFactory, $resource, $resourceCollection, $data, $localeResolver, $productOption, $itemRepository, $searchCriteriaBuilder);
    }

    public function canCancel()
    {
        if (!$this->_canVoidOrder()) {
            return false;
        }
        if ($this->canUnhold()) {
            return false;
        }
        if (!$this->canReviewPayment() && $this->canFetchPaymentReviewUpdate()) {
            return false;
        }

        $allInvoiced = true;

        $conn = $this->resourceConnection->getConnection();
        $select = $conn->select()
            ->from(
                ['sales_item' => 'sales_order_item']
            )
            ->join(
                ['main_table' => 'sales_order'],
                'sales_item.order_id = main_table.entity_id'
            )->where('order_id = ' . $this->getId());

        $items = $conn->fetchAll($select);

        foreach ($items as $item) {
            if ($this->item->isDummy()) {
                $qtyToInvoiced = 0;
            } else {
                $qty = $item['qty_ordered'] - $item['qty_invoiced'] - $item['qty_canceled'];
                $qtyToInvoiced = max(round($qty, 8), 0);
            }
            if ($qtyToInvoiced) {
                $allInvoiced = false;
                break;
            }
        }

        if ($allInvoiced) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }

        return true;
    }
}