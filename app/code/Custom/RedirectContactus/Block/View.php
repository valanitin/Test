<?php


namespace Custom\RedirectContactus\Block;
 
class View extends \Magento\Framework\View\Element\Template
{        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Custom\RedirectContactus\Helper\Data $datahelper,     
        array $data = []
    )
    {      
        $this->datahelper = $datahelper;  
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    
    public function getAction()
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
            if($this->datahelper->getCustomerSessionManager()->isLoggedIn() == true){
            //return "<div class='clsmsgsuccess'>" . $msg . "</div>";
 				return "<div class='message'>We have received your Request & We'll Get in Touch Soon. You can view responses on your email & under My Tickets.</div>";

           } else {
               // return "<div class='clsmsgsuccess'>Your " . $msg . " but you are Guest User that's why you dont't show your Ticket First Login Then you see your Ticket </div>";
				return "<div class='message'>We have received your Request & We'll Get in Touch Soon. You can view responses on your email & under My Tickets.</div>";
			}
        } else if ($status == "error") {
            echo "<div class='clsmsgerror'>" . $msg . "</div>";
        }
    }
}