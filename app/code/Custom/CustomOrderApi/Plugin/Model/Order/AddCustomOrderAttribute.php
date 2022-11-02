<?php

namespace Custom\CustomOrderApi\Plugin\Model\Order;

use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AddCustomOrderAttribute
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param OrderExtensionFactory $extensionFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        OrderExtensionFactory $extensionFactory,
        OrderFactory $orderFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->orderExtensionFactory = $extensionFactory;
        $this->orderFactory = $orderFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set "estimated_shipping" to order data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function setEstimatedShipping(OrderInterface $order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $estimatedShipping = $order->getEstimatedShipping();
        } else {
            $orderModel = $this->orderFactory->create();
            $orderModel->load($order->getId());
            $estimatedShipping = $orderModel->getEstimatedShipping();
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtensionAttributes = $extensionAttributes ? $extensionAttributes
            : $this->orderExtensionFactory->create();
        
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $scopeConfig = $this->scopeConfig;
        $active =  $scopeConfig->getValue("productdeliverydate/delivery_date/active", $storeScope);
        $deliverymaxday =  $scopeConfig->getValue("productdeliverydate/delivery_date/deliverymaxday", $storeScope);
        $deliveryminday =  $scopeConfig->getValue("productdeliverydate/delivery_date/deliveryminday", $storeScope);
        if ($active && $deliverymaxday && $deliveryminday) {
            $date = date('Y-m-d');
            $minday = date('M j', strtotime($date." +".$deliveryminday." days"));
            $maxday = date('M j', strtotime($date." +".$deliverymaxday." days"));
            $orderExtensionAttributes->setEstimatedShipping($minday." - ".$maxday);
        } else {
            $orderExtensionAttributes->setEstimatedShipping('');
        }
        $order->setExtensionAttributes($orderExtensionAttributes);
    }
    
    /**
     * Add "estimated_shipping" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ) {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->setEstimatedShipping($order);
        }
        return $orderSearchResult;
    }

    /**
     * Add "estimated_shipping" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->setEstimatedShipping($resultOrder);
        return $resultOrder;
    }
}