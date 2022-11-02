<?php
namespace Amasty\Checkout\Cron;

use Amasty\Checkout\Api\AdditionalFieldsManagementInterface;
use Amasty\Checkout\Api\Data\CustomFieldsConfigInterface;
use Amasty\Checkout\Model\Config;
use Amasty\Checkout\Model\Delivery;
use Amasty\Checkout\Model\FeeRepository;
use Amasty\Checkout\Model\OrderCustomFieldsFactory;
use Amasty\Checkout\Model\ResourceModel\Delivery as DeliveryResource;
use Amasty\Checkout\Model\ResourceModel\OrderCustomFields;
use Amasty\Checkout\Model\ResourceModel\QuoteCustomFields\CollectionFactory;
use Amasty\Checkout\Model\Subscription;
use LuxuryUnlimited\AmastySorting\Model\ReservationsFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class AfterSubmitObserver
{


    /**
     * @var ReservationsFactory
     */
    protected $reservationsFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * @var AdditionalFieldsManagementInterface
     */
    private $fieldsManagement;
    /**
     * @var Subscription
     */
    private $subscription;
    /**
     * @var FeeRepository
     */
    private $feeRepository;
    /**
     * @var Delivery
     */
    private $delivery;
    /**
     * @var DeliveryResource
     */
    private $deliveryResource;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var OrderCustomFieldsFactory
     */
    private $orderCustomFieldsFactory;
    /**
     * @var OrderCustomFields
     */
    private $orderCustomFieldsResource;

    /**
     * AfterSubmitObserver constructor.
     *
     * @param AdditionalFieldsManagementInterface $fieldsManagement
     * @param Subscription $subscription
     * @param FeeRepository $feeRepository
     * @param Delivery $delivery
     * @param DeliveryResource $deliveryResource
     * @param Config $config
     * @param ReservationsFactory $reservationsFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param OrderCustomFieldsFactory $orderCustomFieldsFactory
     * @param OrderCustomFields $orderCustomFieldsResource
     */
    public function __construct(
        AdditionalFieldsManagementInterface $fieldsManagement,
        Subscription $subscription,
        FeeRepository $feeRepository,
        Delivery $delivery,
        DeliveryResource $deliveryResource,
        Config $config,
        ReservationsFactory $reservationsFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        CartRepositoryInterface $quoteRepository,
        OrderCustomFieldsFactory $orderCustomFieldsFactory,
        OrderCustomFields $orderCustomFieldsResource
    ) {
        $this->fieldsManagement = $fieldsManagement;
        $this->subscription = $subscription;
        $this->feeRepository = $feeRepository;
        $this->quoteRepository = $quoteRepository;
        $this->delivery = $delivery;
        $this->deliveryResource = $deliveryResource;
        $this->config = $config;
        $this->reservationsFactory = $reservationsFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderCustomFieldsFactory = $orderCustomFieldsFactory;
        $this->orderCustomFieldsResource = $orderCustomFieldsResource;
    }

    /**
     * This function call execute
     *
     * This function Call execute
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return $this;
        }
        /** @var  Order $order */

        $data = $this->reservationsFactory->create();
        $firstItem = $data->getCollection()->getLastItem();
        $todayDate = $firstItem->getTimeOfReservations();
        $convertDate = date("Y-m-d h:i:s", strtotime("-1 minutes", strtotime($todayDate)));
        $orders = $this->orderCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter(
                'created_at',
                ['gteq' => $convertDate]
            );
        foreach ($orders as $order) {
            //$order = $observer->getEvent()->getOrder();
            /** @var Quote $quote */
            $quoteId = $order->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            //$quote = $observer->getEvent()->getQuote();
            if (!$order) {
                return $this;
            }
            $orderId = (int)$order->getId();
            $quoteId = (int)$quote->getId();
            $fee = $this->feeRepository->getByQuoteId($quoteId);
            if ($fee->getId()) {
                $fee->setOrderId($orderId);
                $this->feeRepository->save($fee);
            }
            $delivery = $this->delivery->findByQuoteId($quoteId);
            if ($delivery->getId()) {
                $delivery->setData('order_id', $orderId);
                $this->deliveryResource->save($delivery);
            }
            $fields = $this->fieldsManagement->getByQuoteId($quoteId);
            $this->convertCustomFields($quote, $orderId);
            if (!$fields->getId()) {
                return $this;
            }
            if ($fields->getSubscribe()) {
                $this->subscription->subscribe($order->getCustomerEmail());
            }
        }
        return $this;
    }

    /**
     * Convert Custom Fields from Quote to Order
     *
     * @param CartInterface $quote
     * @param int $orderId
     */
    private function convertCustomFields(CartInterface $quote, int $orderId): void
    {
        $shipping = $quote->getShippingAddress();
        $billing = $quote->getBillingAddress();
        foreach (CustomFieldsConfigInterface::CUSTOM_FIELDS_ARRAY as $attributeCode) {
            /** @var \Amasty\Checkout\Model\OrderCustomFields $orderCustomField */
            $orderCustomField = $this->orderCustomFieldsFactory->create(
                ['data' => ['name' => $attributeCode, 'order_id' => $orderId]]
            );
            $orderCustomField->setDataChanges(false);
            $attribute = $shipping->getCustomAttribute($attributeCode);
            if ($attribute) {
                $orderCustomField->setShippingValue($attribute->getValue());
            }
            $attribute = $billing->getCustomAttribute($attributeCode);
            if ($attribute) {
                $orderCustomField->setBillingValue($attribute->getValue());
            }
            if ($orderCustomField->hasDataChanges()) {
                $this->orderCustomFieldsResource->save($orderCustomField);
            }
        }
    }

}
