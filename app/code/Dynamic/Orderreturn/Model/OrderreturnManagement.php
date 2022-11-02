<?php
declare(strict_types=1);

namespace Dynamic\Orderreturn\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Orderreturn\Api\OrderreturnManagementInterface;

/**
 * Class OrderreturnManagement
 *
 */
class OrderreturnManagement implements OrderreturnManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $orderreturn;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->orderreturn = $orderreturn;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($returnForm) {
        
        $result = $this->dataObjectFactory->create();

        $response = [];

        if(empty($returnForm)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }

        if (empty($returnForm['order_id'])) {
            $result->setData('message', 'Enter the Order Id and try again.');
            return $result;
        }

        if (empty($returnForm['product_sku'])) {
            $result->setData('message', 'Enter the Product Sku and try again.');
            return $result;
        }

        if (empty($returnForm['customer_email'])) {
            $result->setData('message', 'Enter the Customer Email and try again.');
            return $result;
        }
        
        if($this->validateEmailFormat($returnForm['customer_email'])){
            $result->setData('message', 'Please enter a valid email address.');
            return $result;
        }
        
        if (empty($returnForm['type'])) {
            $result->setData('message', 'Enter the Type and try again.');
            return $result;
        }

        if (empty($returnForm['reason'])) {
            $result->setData('message', 'Enter the Return Reason and try again.');
            return $result;
        }

        if (empty($returnForm['lang_code'])) {
            $result->setData('message', 'Enter the Lang Code and try again.');
            return $result;
        }

        if (empty($returnForm['website'])) {
            $result->setData('message', 'Enter the Website and try again.');
            return $result;
        }
        $applied = $this->orderreturn->getCollection()->addfieldtofilter('product_sku', $returnForm['product_sku'])->addfieldtofilter('order_id', $returnForm['order_id']);
        if($returnForm && $applied->getSize() <= 0){
            $this->orderreturn->setData($returnForm);
            try {
                $this->orderreturn->save();
                $result->setData('message', 'Order return request successfully sent, you will get updates soon.');
                return $result;

            } catch (\Exception $e) {
                $result->setData('message', $e->getMessage());
                return $result;
            }
        }else{
            $result->setData('message', 'You have already applied for return.');
            return $result;
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateEmailFormat($email) {

        if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
            return true;
        }
        return false;
    }
}