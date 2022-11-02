<?php

namespace LuxuryUnlimited\MyReturnsApi\Helper;

use Exception;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class ReturnAndCancel
{
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @param JsonSerializer  $jsonSerializer
     * @param LoggerInterface $logger
     * @param Curl            $curl
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        LoggerInterface $logger,
        Curl $curl
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->logger         = $logger;
        $this->curl           = $curl;
    }

    /**
     * Get is_cancellable value from the ERP
     *
     * @param OrderInterface|Order|OrderItemInterface $order
     * @param null|bool                               $items
     *
     * @return int
     */
    public function isReturnable($order, $items = false)
    {
        try {
            $sku = false;
            if ($items) {
                $orderItems = $order;
                $sku        = $orderItems->getSku();
                $order      = $order->getOrder();

            }
            $checkReturnUrl = "https://erp.theluxuryunlimited.com/api/order/check-return";

            $orderId  = $order->getIncrementId() ?? $order->getRealOrderId();
            $postData = [
                "website"  => "WWW.SOLOLUXURY.COM",
                "order_id" => $orderId,
            ];
            if ($sku) {
                $postData["product_sku"] = $sku;
            }
            $curl = $this->curl;
            $curl->post($checkReturnUrl, $postData);
            $response  = $curl->getBody();
            $data      = $this->jsonSerializer->unserialize($response);
            $apiStatus = $data['code'];

            if ($apiStatus == 200 && isset($data['data'])) {
                if (!$data['data']['has_return_request']) {
                    return 0;
                }

                return 1;
            }

            return 0;
        } catch (Exception $exception) {
            $this->logger->error('https://erp.theluxuryunlimited.com/api/order/check-return - API Failed');
            $this->logger->error($exception->getMessage());

            return 0;
        }
    }

    /**
     * Get is_cancellable value from the ERP
     *
     * @param OrderInterface|Order|OrderItemInterface $order
     * @param null|bool                               $items
     *
     * @return bool
     */
    public function isCancellable($order, $items = false)
    {
        $orderId   = $order->getRealOrderId();
        $orderItem = $order;

        if ($items) {
            $orderId   = $order->getOrder()->getRealOrderId();
            $orderItem = $order;
            $order     = $order->getOrder();
            $state     = $order->getState();
            if ($order->isCanceled() || $state === 'complete' || $state === 'closed') {
                return 0;
            }

        }

        if (!$items && !$order->canCancel()) {
            return 0;
        }
        $postData = [
            'website'  => 'WWW.SOLOLUXURY.COM',
            'order_id' => $orderId
        ];

        if ($items) {
            $postData['product_sku'] = $orderItem->getSku();
        }
        try {
            $canCancel = $this->checkIsCancellableInErp($postData);
        } catch (Exception $exception) {
            $this->logger->error('https://erp.theluxuryunlimited.com/api/order/check-cancellation - API Failed');
            $this->logger->error($exception->getMessage());

            return 0;
        }

        return $canCancel;
    }

    /**
     * Check whethere the Order is eligible for Cancel in the ERP
     *
     * @param array $postParams
     *
     * @return int
     */
    public function checkIsCancellableInErp($postParams)
    {

        $url  = 'https://erp.theluxuryunlimited.com/api/order/check-cancellation';
        $curl = $this->curl;
        $curl->post($url, $postParams);
        $response  = $curl->getBody();
        $data      = $this->jsonSerializer->unserialize($response);
        $apiStatus = $data['code'];

        if ($apiStatus == 200 && isset($data['data'])) {
            if (!$data['data']['iscanceled'] && $data['data']['isrefund']) {
                return 1;
            }

            return 0;
        }

        return 0;
    }
}
