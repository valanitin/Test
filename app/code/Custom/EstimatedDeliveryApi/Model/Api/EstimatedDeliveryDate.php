<?php

namespace Custom\EstimatedDeliveryApi\Model\Api;

use Psr\Log\LoggerInterface;
use Custom\EstimatedDeliveryApi\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;

class EstimatedDeliveryDate
{
    protected $helper;
    protected $logger;
    protected $productrepository;

    public function __construct(LoggerInterface $logger, Data $helper,ProductRepositoryInterface $productrepository)
    {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->productrepository = $productrepository;
    }

    /**
    * @inheritdoc
    */

    public function getEstimateDate($id)
    {
        try {
            $product = $this->productrepository->getById($id);
            $active = $this->helper->getConfig("productdeliverydate/delivery_date/active");
            $deliverymaxday = $this->helper->getConfig("productdeliverydate/delivery_date/deliverymaxday");
            $deliveryminday = $this->helper->getConfig("productdeliverydate/delivery_date/deliveryminday");
            $dateObj = $this->helper->getDate();
            $date = $dateObj->date('Y-m-d');
            $minday = $dateObj->date('M j', strtotime($date." +".$deliveryminday." days"));
            $maxday = $dateObj->date('M j', strtotime($date." +".$deliverymaxday." days"));
            if ($product->isSaleable() && $active) {
                $response = ['title' => 'Estimated Delivery Date: '. $minday." - ".$maxday];
            } else {
                $response = ['success' => false, 'message' => 'The product is out of stock.'];
            }
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            $this->logger->info($e->getMessage());
        }
        $returnArray = json_encode($response);
        return $response;
    }
}