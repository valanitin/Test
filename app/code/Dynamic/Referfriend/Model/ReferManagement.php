<?php
declare(strict_types=1);

namespace Dynamic\Referfriend\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Referfriend\Api\ReferTofriendManagementInterface;

/**
 * Class ReferManagement
 *
 */
class ReferManagement implements ReferTofriendManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $referfriend;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Dynamic\Referfriend\Model\Referfriend $referfriend
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->referfriend = $referfriend;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($referForm) {
        
        $result = $this->dataObjectFactory->create();

        $response = [];

        if(empty($referForm)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }

        if (empty($referForm['referrer_first_name'])) {
            $result->setData('message', 'Enter the Referrer First Name and try again.');
            return $result;
        }
        if (empty($referForm['referrer_email'])) {
            $result->setData('message', 'Enter the Referrer Email and try again.');
            return $result;
        }
        if (false === \strpos($referForm['referrer_email'], '@') || false === \strpos($referForm['referrer_email'], '.com')) {
            $result->setData('message', 'The referrer email address is invalid. Verify the referrer email address and try again.');
            return $result;
        }
        if (empty($referForm['referrer_phone'])) {
            $result->setData('message', 'Enter the Referrer Phone and try again.');
            return $result;
        }

        if (empty($referForm['your_first_name'])) {
            $result->setData('message', 'Enter the Your First Name and try again.');
            return $result;
        }
        if (empty($referForm['your_email'])) {
            $result->setData('message', 'Enter the Your Email and try again.');
            return $result;
        }
        if (false === \strpos($referForm['your_email'], '@') || false === \strpos($referForm['your_email'], '.com')) {
            $result->setData('message', 'The your email address is invalid. Verify the your email address and try again.');
            return $result;
        }
        if (empty($referForm['your_phone'])) {
            $result->setData('message', 'Enter the Your Phone and try again.');
            return $result;
        }

        $referfriendData = [
            "referrer_first_name" => $referForm["referrer_first_name"],
            "referrer_email" => $referForm["referrer_email"],
            "referrer_phone" => $referForm["referrer_phone"],
            "yourfirstname" => $referForm["your_first_name"],
            "youremailaddress" => $referForm["your_email"],
            "yourphonenumber" => $referForm["your_phone"]
        ];

        $this->referfriend->setData($referfriendData);
        try {
            $this->referfriend->save();
            $result->setData('message', 'Request Successfully Sent, You will get Referrer ID shortly.');
            return $result;

        } catch (\Exception $e) {
            $result->setData('message', $e->getMessage());
            return $result;
        }
    }
}