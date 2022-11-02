<?php

namespace LuxuryUnlimited\MyReturnsApi\Model\Order;

use Dynamic\Customization\Helper\Data as DynamicHelper;
use Exception;
use LuxuryUnlimited\MyReturnsApi\Api\OrderCancelInterface;
use LuxuryUnlimited\MyReturnsApi\Model\Order\Cancel\UpdateOrder;
use LuxuryUnlimited\MyReturnsApi\Model\Order\Cancel\UpdateOrderItems;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Cancel implements OrderCancelInterface
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var OrderRepositoryInterface
     */
    protected $order;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var DynamicHelper
     */
    protected $dynamicHelper;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var UpdateOrderItems
     */
    protected $updateOrderItems;
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    /**
     * @var UpdateOrder
     */
    protected $updateOrder;

    /**
     * @param DataObjectFactory        $dataObjectFactory
     * @param OrderRepositoryInterface $order
     * @param LoggerInterface          $logger
     * @param Curl                     $curl
     * @param JsonSerializer           $jsonSerializer
     * @param DynamicHelper            $dynamicHelper
     * @param UpdateOrderItems         $updateOrderItems
     * @param UpdateOrder              $updateOrder
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        OrderRepositoryInterface $order,
        LoggerInterface $logger,
        Curl $curl,
        JsonSerializer $jsonSerializer,
        DynamicHelper $dynamicHelper,
        UpdateOrderItems $updateOrderItems,
        UpdateOrder $updateOrder,
        OrderManagementInterface $orderManagement
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->order             = $order;
        $this->logger            = $logger;
        $this->curl              = $curl;
        $this->dynamicHelper     = $dynamicHelper;
        $this->jsonSerializer    = $jsonSerializer;
        $this->updateOrderItems  = $updateOrderItems;
        $this->orderManagement   = $orderManagement;
        $this->updateOrder       = $updateOrder;
    }

    /**
     * @inheritDoc
     */
    public function cancelOrder($cancelForm)
    {
        $result = $this->dataObjectFactory->create();

        $requiredParams = ['order_id', 'reason', 'lang_code'];
        $validation     = $this->validateForm($cancelForm, $requiredParams);

        if ($validation['error']) {
            $missingValues = implode(",", $validation['data']);
            $result->setData('error', true);
            $result->setData('message', 'Please enter required fields. ' . $missingValues);

            return $result;
        }
        try {
            $order        = $this->order->get($cancelForm['order_id']);
            $cancelStatus = $this->cancelOrderAtErp($order, $cancelForm);
            if (isset($cancelStatus['errors']) && $cancelStatus['errors'] !== 'true') {
                // Reusing the same code which completed earlier
                $cancelStatus = $this->updateOrder->updateOrder($order, $cancelForm);
                $result->setData('error', $cancelStatus['errors']);
                $result->setData('message', $cancelStatus['message']);
            }
        } catch (Exception $exception) {
            $result->setData('error', true);
            $result->setData('message', $exception->getMessage());
        }

        return $result;
    }

    /**
     * Validate the Post Params - Form
     *
     * @param array $formData
     * @param array $requiredParams
     *
     * @return array
     */
    public function validateForm($formData, $requiredParams)
    {
        $availableData = array_keys($formData);
        $difference    = array_diff($requiredParams, $availableData);
        if ($difference) {
            return ['error' => true, 'data' => $difference];
        }

        return ['error' => false, 'data' => $difference];
    }

    /**
     *  Cancel the Order at Erp
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    public function cancelOrderAtErp($order)
    {
        try {
            $response = $this->cancelMagentoOrder($order->getId());

            return ['errors' => false, 'message' => __($response)];

        } catch (Exception $exception) {
            return ['errors' => true, 'message' => $exception->getMessage()];
        }
    }

    /**
     * Cancel the Magento Order
     *
     * @param int|string $orderId
     *
     * @return Phrase
     */
    public function cancelMagentoOrder($orderId)
    {
        try {
            $this->orderManagement->cancel($orderId);

            return __('Cancellation request sent successfully.');
        } catch (Exception $e) {
            return __('You have not canceled the order.');
        }
    }

    /**
     * @inheritDoc
     */
    public function cancelOrderItem($cancelForm)
    {
        $result = $this->dataObjectFactory->create();

        $requiredParams = ['order_id', 'item_id', 'reason', 'lang_code'];
        $validation     = $this->validateForm($cancelForm, $requiredParams);

        if ($validation['error']) {
            $missingValues = implode(",", $validation['data']);
            $result->setData('error', true);

            $result->setData('message', 'Please enter required fields. ' . $missingValues);

            return $result;
        }
        try {
            $order        = $this->order->get($cancelForm['order_id']);
            $cancelStatus = $this->cancelOrderItemAtErp($order, $cancelForm);
            if (isset($cancelStatus['status']) && $cancelStatus['status'] !== 'failed') {
// Reusing the same code which completed earlier
                $cancelStatus = $this->updateOrderItems->updateOrderItems($order, $cancelForm);
                $result->setData('error', $cancelStatus['errors']);
                $result->setData('message', $cancelStatus['message']);
            }
        } catch (Exception $exception) {
            $result->setData('error', true);
            $result->setData('message', $exception->getMessage());
        }

        return $result;
    }

    /**
     * Cancel the order at the ERP
     *
     * @param orderInterface $order
     * @param array          $formData
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function cancelOrderItemAtErp(
        $order,
        $formData
    ) {

        $dynamicHelper = $this->dynamicHelper;
        $siteUrl       = $dynamicHelper->getScopeConfig()->getValue("web/secure/base_url");
        $fileUrl       = $siteUrl . 'apifiles/orderCancel.php';
        $curl          = $this->curl;
        $postParams    =
            [
                'customer_email' => $order->getCustomerEmail(),
                "website"        => "WWW.SOLOLUXURY.COM",
                "order_id"       => $order->getIncrementId(),
                "type"           => "cancellation",
                "reason"         => $formData['reason'],
                "lang_code"      => $formData['lang_code']
            ];
        $curl->post($fileUrl, $postParams);

        $response = $curl->getBody();

        return $this->jsonSerializer->unserialize($response);
    }
}
