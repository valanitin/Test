<?php
declare(strict_types=1);

namespace Dynamic\Notifyme\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Notifyme\Api\NotifystockManagementInterface;

/**
 * Class NotifymeManagement
 *
 */
class NotifystockManagement implements NotifystockManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $notifyme;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Dynamic\Notifyme\Model\Notifyme $notifyme
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->notifyme = $notifyme;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($notifymeForm) {
        
        $result = $this->dataObjectFactory->create();

        $response = [];

        if(empty($notifymeForm)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }

        if (empty($notifymeForm['email'])) {
            $result->setData('message', 'Enter the Email and try again.');
            return $result;
        }
        if (false === \strpos($notifymeForm['email'], '@') || false === \strpos($notifymeForm['email'], '.com')) {
            $result->setData('message', 'The email address is invalid. Verify the email address and try again.');
            return $result;
        }
        if (empty($notifymeForm['product_sku'])) {
            $result->setData('message', 'Enter the Product SKU and try again.');
            return $result;
        }

        $notifymeData = [
            "email" => $notifymeForm["email"],
            "product_sku" => $notifymeForm["product_sku"],
            "product_size" => isset($notifymeForm["product_size"]) ? $notifymeForm["product_size"] : ""
        ];

        $this->notifyme->setData($notifymeData);
        try {
            $this->notifyme->save();
            $result->setData('message', 'Request Successfully Sent.');
            return $result;

        } catch (\Exception $e) {
            $result->setData('message', $e->getMessage());
            return $result;
        }
    }
}