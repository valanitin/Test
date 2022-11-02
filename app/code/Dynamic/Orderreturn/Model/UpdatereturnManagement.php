<?php
declare(strict_types=1);

namespace Dynamic\Orderreturn\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Orderreturn\Api\UpdatereturnManagementInterface;

/**
 * Class UpdatereturnManagement
 *
 */
class UpdatereturnManagement implements UpdatereturnManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $orderreturn;

    protected $orderreturnCollection;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn,
        \Dynamic\Returnstatus\Model\ResourceModel\Returnstatus\Collection $orderreturnCollection
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->orderreturn = $orderreturn;
        $this->orderreturnCollection = $orderreturnCollection;
    }

    /**
    * @inheritDoc
    */
    public function statusReturnForm($statusReturn) {
        
        $result = $this->dataObjectFactory->create();

        $response = [];

        if(empty($statusReturn)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }

        if (empty($statusReturn['order_id'])) {
            $result->setData('message', 'Enter the Order Id and try again.');
            return $result;
        }

        if (empty($statusReturn['product_sku'])) {
            $result->setData('message', 'Enter the Product Sku and try again.');
            return $result;
        }

        $orderReturnCollection = $this->orderreturn->getCollection()->addFieldToFilter('product_sku', $statusReturn['product_sku'])->addFieldToFilter('order_id', $statusReturn['order_id']);

        if(!empty($orderReturnCollection) && count($orderReturnCollection) > 0){

            try {
                $statusArr = $this->getReturnStatus();

                if(!empty($statusArr) && in_array(strtolower($statusReturn["status"]), $statusArr)) {
                    foreach ($orderReturnCollection as $orderReturn) {
                       $orderReturn->setErpReturnStatus(strtolower($statusReturn["status"]));
                       $orderReturn->save();
                    }
                    $result->setData('message', 'Order return status update successfully.');
                    return $result;
                } else {
                    $result->setData('message', 'Order return status not exists.');
                    return $result;
                }

            } catch (\Exception $e) {
                $result->setData('message', $e->getMessage());
                return $result;
            }
        }else{
            $result->setData('message', 'Order return not exists.');
            return $result;
        }
    }

    public function getReturnStatus() {

        $status = [];
        if(!empty($this->orderreturnCollection->getData())) {
            foreach($this->orderreturnCollection as $orderReturn) {
                $status[] = strtolower($orderReturn->getStatus());
            }
        }
        return $status;
    }
}