<?php
declare(strict_types=1);

namespace Dynamic\NotifyMeApi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\NotifyMeApi\Api\NotifyMeManagementInterface;

/**
 * Class NotifyMeManagement
 *
 */
class NotifyMeManagement implements NotifyMeManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $scopeConfig;

    protected $curlFactory;
    
    protected $encoder;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\Json\EncoderInterface $encoder
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->encoder = $encoder;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($email,$website,$sku, $size = null) {
        
        $url = "https://erp.theluxuryunlimited.com/api/out-of-stock-subscription";
        $paramsData = array(
            'website' => $website,
            'email' => $email,
            'sku'=> $sku
        );
        if ($size) {
            $paramsData['size'] = $size;
        }

        $curl = $this->curlFactory->create();
        $curl->setOption(CURLOPT_RETURNTRANSFER, 1);
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        try{
            $this->validateEmailFormat($email);
            $data = [];
            $curl->post($url, $this->encoder->encode($paramsData));
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

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid email address.'));
        }
    }
}