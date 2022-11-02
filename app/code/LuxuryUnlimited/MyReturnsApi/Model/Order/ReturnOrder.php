<?php

namespace LuxuryUnlimited\MyReturnsApi\Model\Order;

use Exception;
use LuxuryUnlimited\MyReturnsApi\Api\Data\OrderReturnDataInterface;
use LuxuryUnlimited\MyReturnsApi\Api\OrderReturnInterface;
use LuxuryUnlimited\MyReturnsApi\Helper\PostReturn;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class ReturnOrder implements OrderReturnInterface
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
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var PostReturn
     */
    protected $postReturn;

    /**
     * @param DataObjectFactory        $dataObjectFactory
     * @param OrderRepositoryInterface $order
     * @param LoggerInterface          $logger
     * @param Curl                     $curl
     * @param JsonSerializer           $jsonSerializer
     * @param PostReturn               $postReturn
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        OrderRepositoryInterface $order,
        LoggerInterface $logger,
        Curl $curl,
        JsonSerializer $jsonSerializer,
        PostReturn $postReturn
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->order             = $order;
        $this->logger            = $logger;
        $this->curl              = $curl;
        $this->jsonSerializer    = $jsonSerializer;
        $this->postReturn        = $postReturn;
    }

    /**
     *  Return the Entire Order and create the Ticket
     *
     * @param mixed $returnForm
     *
     * @return DataObject
     */
    public function returnOrder($returnForm)
    {
        $result = $this->dataObjectFactory->create();

        $requiredParams = ['order_id', 'reason', 'lang_code'];
        $validation     = $this->validateForm($returnForm, $requiredParams);

        if ($validation['error']) {
            $missingValues = implode(",", $validation['data']);
            $result->setData('error', true);

            $result->setData('message', 'Please enter required fields. ' . $missingValues);

            return $result;
        }
        try {
            $order        = $this->order->get($returnForm['order_id']);
            $returnStatus = $this->postReturn->returnOrder($order, $returnForm);
            $result->setData('error', $returnStatus['errors']);
            $result->setData('message', $returnStatus['message']);
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
     * Return the Order Item
     *
     * @param mixed $returnForm
     *
     * @return OrderReturnDataInterface|DataObject
     */
    public function returnOrderItem($returnForm)
    {
        $result = $this->dataObjectFactory->create();

        $requiredParams = ['order_id', 'item_id', 'reason', 'lang_code'];
        $validation     = $this->validateForm($returnForm, $requiredParams);

        if ($validation['error']) {
            $missingValues = implode(",", $validation['data']);
            $result->setData('error', true);

            $result->setData('message', 'Please enter required fields. ' . $missingValues);

            return $result;
        }
        try {
            $order        = $this->order->get($returnForm['order_id']);
            $returnStatus = $this->postReturn->returnOrderItem($order, $returnForm);
            $result->setData('error', $returnStatus['errors']);
            $result->setData('message', $returnStatus['message']);
        } catch (Exception $exception) {
            $result->setData('error', true);
            $result->setData('message', $exception->getMessage());
        }

        return $result;
    }
}
