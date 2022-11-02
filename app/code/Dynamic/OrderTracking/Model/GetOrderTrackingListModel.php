<?php

namespace Dynamic\OrderTracking\Model;

use Dynamic\OrderTracking\Api\GetOrderTracking;

class GetOrderTrackingListModel implements GetOrderTracking
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * Helper OrderTracking
     *
     * @var \Dynamic\OrderTracking\Helper\Data
     */
    protected $helperOrderTracking;

    /**
     * Size data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Dynamic\OrderTracking\Helper\Data $helperOrderTracking
     * @param \Magento\Sales\Model\Order $sales
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Dynamic\OrderTracking\Helper\Data $helperOrderTracking,
        \Magento\Sales\Model\Order $order
    ) {
        $this->helperOrderTracking = $helperOrderTracking;
        $this->order = $order;
    }

    /**
     * Returns OrderTracking data
     *
     * @api
     * @return return OrderTracking array collection.
     */
    public function getOrderTrackingList($orderId)
    {
        $data = [];

        $order = $this->order->loadByIncrementId($orderId);

        if(!empty($order) && $order->getId()) {
            $orderTrackingData = $this->helperOrderTracking->getOrderStatusList($order);
            if(!empty($orderTrackingData) && count($orderTrackingData) > 0) {
                $data = $orderTrackingData;
            } else {
                $data = [
                    ['status' => 'No Data','message' => __('There are no Order Tracking data in this website.') ]
                ];
            }
        } else {
            $data = [
                ['status' => 'No Data','message' => __('This Order not exists in this website.') ]
            ];
        }
        
        return $data;
    }
}
