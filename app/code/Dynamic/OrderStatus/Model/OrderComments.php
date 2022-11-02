<?php

namespace Dynamic\OrderStatus\Model;

use Dynamic\OrderStatus\Api\OrderManagementInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as OrderStatusCollection;
use Magento\Catalog\Model\Product;
use Psr\Log\LoggerInterface;

class OrderComments implements OrderManagementInterface {

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository     
     * @param LoggerInterface $logger
     */
    public function __construct(
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, OrderStatusCollection $orderStatusCollection, Product $product, LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->_product = $product;
        $this->orderStatusCollection = $orderStatusCollection;
        $this->logger = $logger;
    }

    /**
     * Add comment to order
     *
     * @param int $id
     * @param string[] $value
     * @return array
     */
    public function OrderComment($id, $value) {

        $respones_array = array();

        $order = $this->orderRepository->get($id);
        $orderItems = $order->getAllVisibleItems();
        $orderstatus = $this->orderStatusCollection->toOptionArray();
        foreach ($orderstatus as $status) {
            $order_status[] = $status['value'];
        }


        if (!empty($value['ProductSku'])) {
            $product_sku_array = explode(",", $value['ProductSku']);

            foreach ($product_sku_array as $product_sku) {

                if ($this->_product->getIdBySku($product_sku)) {
                    /* $this->_logger->info('Product is Exist'); */
                    foreach ($orderItems as $item) {
                        $order_sku[] = $item->getSku();
                    }
                    if (in_array($product_sku, $order_sku)) {

                        if (in_array($value['status'], $order_status)) {
                            try {
                                $order->addCommentToStatusHistory($value['comment']);
                                $order->setState($value['status'])->setStatus($value['status']);
                                $this->orderRepository->save($order);
                                $respones_array = array(
                                    "status" => true,
                                    "message" => "Order Status Successfully Changes"
                                );
                            } catch (\Exception $e) {
                                $respones_array = array(
                                    "status" => false,
                                    "message" => $e->getMessage()
                                );
                            }
                        } else {
                            $respones_array = array(
                                "status" => false,
                                "message" => "Does not Exist This order Status on Magento."
                            );
                        }
                    } else {
                        $respones_array = array(
                            "status" => false,
                            "message" => "Does not Exist This " . $product_sku . " Sku on Our Order."
                        );
                    }
                } else {
                    $respones_array = array(
                        "status" => false,
                        "message" => "Does not Exist This " . $product_sku . " Sku on Magento."
                    );
                }
            }
        } else {
            if (in_array($value['status'], $order_status)) {
                try {
                    $order->addCommentToStatusHistory($value['comment']);
                    $order->setState($value['status'])->setStatus($value['status']);
                    $this->orderRepository->save($order);
                    $respones_array = array(
                        "status" => true,
                        "message" => "Order Status Successfully Changes."
                    );
                } catch (\Exception $e) {
                    $respones_array = array(
                        "status" => false,
                        "message" => $e->getMessage()
                    );
                }
            } else {
                $respones_array = array(
                    "status" => false,
                    "message" => "Does not Exist This order Status on Magento."
                );
            }
        }





        $respone_json = json_encode($respones_array);

        return $respone_json;
    }

}
