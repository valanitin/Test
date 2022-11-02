<?php
declare(strict_types=1);

namespace Local\BrowserPushNotification\Controller\Pushnotik;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Check firebase token for notification
 */
class Regtoken extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var CurlFactory
     */
    protected $_curlFactory;

    /**
     * @var Data
     */
    protected $_jsonHelper;
 
    /**
     * @param Context             $context
     * @param JsonFactory         $resultJsonFactory
     * @param CurlFactory         $curlFactory
     * @param Data                $jsonHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CurlFactory $curlFactory,
        Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory   = $resultJsonFactory;
        $this->_curlFactory = $curlFactory;
        $this->_jsonHelper = $jsonHelper;
    }
 
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultJson = $this->_resultJsonFactory->create();

        if (isset($data['website']) && isset($data['token'])) {
            $url = 'https://erp.theluxuryunlimited.com/api/notification/create';
            $requstbody = [
                'website'=> $data['website'],
                'token'=> $data['token']
            ];
            
            /* Create curl factory */
            $httpAdapter = $this->_curlFactory->create();
            /* Forth parameter is POST body */
            $httpAdapter->write(
                (string)\Zend_Http_Client::POST,
                (string)$url,
                (string)'1.1',
                ["Content-Type:application/json", "Accept:application/json"],
                (string)json_encode($requstbody)
            );
            $result = $httpAdapter->read();
            $body = \Zend_Http_Response::extractBody($result);
            /* convert JSON to Array */
            $response = $this->_jsonHelper->jsonDecode($body);

            if ($response['status'] == "success") {
                return $resultJson->setData([
                    'success' => 1
                ]);
            } else {
                return $resultJson->setData([
                    'success' => 0
                ]);
            }
        } else {
            return $resultJson->setData([
                    'success' => 0
            ]);
        }
    }
}
