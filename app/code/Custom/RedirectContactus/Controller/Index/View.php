<?php

namespace Custom\RedirectContactus\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class View extends Action
{
    /**
     * The PageFactory to render with.
     *
     * @var PageFactory
     */
    protected $_resultsPageFactory;

    /**
     * Set the Context and Result Page Factory from DI.
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->_resultsPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $status = "";
        $msg = "";
        
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $filterarray = array(
                "name" => @$_REQUEST['name'],
                "last_name" => @$_REQUEST['surname'],
                "email" => @$_REQUEST['email'],
                "type_of_inquiry" => @$_REQUEST['typeofenquiry'],
                "order_no" => @$_REQUEST['orderno'],
                "country" => @$_REQUEST['country'],
                "subject" => @$_REQUEST['subject'],
                "message" => @$_REQUEST['comment'],
                "source_of_ticket" => $this->_storeManager->getStore()->getBaseUrl(),
                "phone_no" => @$_REQUEST['telephone']
            );
            //$logger->info($filterarray);
            $header = array(
                'Accept: application/json',
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://erp.theluxuryunlimited.com/api/ticket/create");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filterarray));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            $serverOutput = json_decode($server_output);
            if ($serverOutput->status == "success") {
                $msg = $serverOutput->message;
                $status = "success";
            } else {
                $msg = "Unable to create ticket";
                $status = "error";
            }
        
        if ($status == "success") {
            $this->messageManager->addSuccess($msg);
            //echo "<div class='clsmsgsuccess'>" . $msg . "</div>";
        } else if ($status == "error") {
            $this->messageManager->addError($msg);
            //echo "<div class='clsmsgerror'>" . $msg . "</div>";
        }
        $resultPage = $this->_resultsPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
            ->createBlock('Custom\RedirectContactus\Block\View')
            ->setTemplate('Custom_RedirectContactus::contactsubmit.phtml')
            ->toHtml();
        $this->getResponse()->setBody($block);
    }
}
