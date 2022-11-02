<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace Dynamic\Mytickets\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddMessage extends Action
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    
    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
        CurlFactory $curlFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->curlFactory = $curlFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Add Message
     *
     * @return Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->sendErpTicketMessage($params);
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('tickets/customer/index/');
        return $resultRedirect;
    }

    /**
     * @param $params
     * @return void
     */
    protected function sendErpTicketMessage($params){
        $url = "https://erp.theluxuryunlimited.com/api/ticket/send";
        $data = array(
            "website" => "www.sololuxury.com",
            "ticket_id" => $params['id'],
            "message" => $params['message'],
            "action" => "send_messsage"
        );
        $curl = $this->curlFactory->create();
        $curl->setOption(CURLOPT_POST, true);
        $curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $headers = array("Content-Type" => "application/json");
        $curl->setHeaders($headers);
        $curl->post($url, json_encode($data));
        $result   = $curl->getBody();
        $response = [json_decode($result,true)];
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
