<?php

declare(strict_types=1);

namespace Dynamic\Mytickets\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Mytickets\Api\GetMyticketsListInterface;

/**
 * Class ReferManagement
 *
 */
class GetMyticketsListModel implements GetMyticketsListInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $curlFactory;
    
    protected $encoder;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\Json\EncoderInterface $encoder
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->curlFactory = $curlFactory;
        $this->encoder = $encoder;
    }

    /**
    * @inheritDoc
    */
    public function getList() {

        $website = $this->request->getPostValue("website");
        $email = $this->request->getPostValue("email");

        if(empty($website)) {
            $data = array(
                [   'status' => 'No Data',
                    'message' => __('Please enter website field.') 
                ]
            );
            return $data;
        }

        if(empty($email)) {
            $data = array(
                [   'status' => 'No Data',
                    'message' => __('Please enter email field.') 
                ]
            );
            return $data;
        }

        if (false === \strpos($email, '@') || false === \strpos($email, '.com')) {
            $data = array(
                [   'status' => 'No Data',
                    'message' => __('The email address is invalid. Verify the email address and try again.') 
                ]
            );
            return $data;
        }

        $postData = [
            'website' => $website,
            'email' => $email
        ];

        $url = "https://erp.theluxuryunlimited.com/api/ticket/send";

        $paramsData = $this->encoder->encode($postData);
        
        $curl = $this->curlFactory->create();

        $curl->setOption(CURLOPT_RETURNTRANSFER, 1);

        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        try{

            $data = [];

            $curl->post($url, $paramsData);

            $result = $curl->getBody();

            $response = array(json_decode($result,true));

            return $response;

        }catch(Exception $e){
            $data = array(
                [   'status' => 'Error',
                    'message' => $e->getMessage()
                ]
            );
            return $data;
        }
    }
}