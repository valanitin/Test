<?php
declare(strict_types=1);

namespace Dynamic\Abandonedcartapi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Abandonedcartapi\Api\AbandonedcartapiManagementInterface;

/**
 * Class AbandonedcartapiManagement
 *
 */
class AbandonedcartapiManagement implements AbandonedcartapiManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $abandonedcartapi;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Dynamic\Abandonedcartapi\Model\Abandonedcartapi $abandonedcartapi
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->abandonedcartapi = $abandonedcartapi;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($cartInfo) {
        
        $result = $this->dataObjectFactory->create();

        $response = [];

        if(empty($cartInfo)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }
        if (empty($cartInfo['name'])) {
            $result->setData('message', 'Enter the name and try again.');
            return $result;
        }
        if (empty($cartInfo['email'])) {
            $result->setData('message', 'Enter the email and try again.');
            return $result;
        }
        if (false === \strpos($cartInfo['email'], '@') || false === \strpos($cartInfo['email'], '.com')) {
            $result->setData('message', 'The email address is invalid. Verify the email address and try again.');
            return $result;
        }
        if (empty($cartInfo['lang_code'])) {
            $result->setData('message', 'Enter the lang code and try again.');
            return $result;
        }
        if (empty($cartInfo['item_info'])) {
            $result->setData('message', 'Enter the item info and try again.');
            return $result;
        }
        
        $abandonedcartapiData = [
            "name" => $cartInfo["name"],
            "email" => $cartInfo["email"],
            "lang_code" => $cartInfo["lang_code"],
            "item_info" => (!empty($cartInfo["item_info"]) && count($cartInfo["item_info"]) > 0) ? json_encode($cartInfo["item_info"]) : '',
        ];

        $this->abandonedcartapi->setData($abandonedcartapiData);
        try {
            $this->abandonedcartapi->save();
            $result->setData('message', 'Abandoned Cart Data Successfully Added.');
            return $result;

        } catch (\Exception $e) {
            $result->setData('message', $e->getMessage());
            return $result;
        }
    }
}