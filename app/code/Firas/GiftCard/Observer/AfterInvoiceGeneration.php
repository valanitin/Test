<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

class AfterInvoiceGeneration implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesOrder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_catalpgProduct;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_magentoSalesRule;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_magentoSalesOrderItem;

    /**
     * @var \Firas\GiftCard\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Firas\GiftCard\Model\GiftDetailFactory
     */
    protected $_giftDetailFactory;

    /**
     * @var \Firas\GiftCard\Model\GiftUserFactory
     */
    protected $_giftUserFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    const XML_PATH_ERP_API_URL = "custom/erp_api/erp_api_url";

    /**
     *
     * @param \Magento\Sales\Model\Order                           $salesOrder
     * @param \Magento\Catalog\Model\Product                       $catalpgProduct
     * @param \Magento\SalesRule\Model\Rule                        $magentoSalesRule
     * @param \Magento\Sales\Model\Order\ItemFactory               $magentoSalesOrderItem
     * @param \Firas\GiftCard\Helper\Data                        $helperData
     * @param \Firas\GiftCard\Model\GiftDetailFactory            $giftDetailFactory
     * @param \Firas\GiftCard\Model\GiftUserFactory              $giftUserFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\Message\ManagerInterface          $messageManager
     */
    public function __construct(
        \Magento\Sales\Model\Order $salesOrder,
        \Magento\Catalog\Model\Product $catalpgProduct,
        \Magento\SalesRule\Model\Rule $magentoSalesRule,
        \Magento\Sales\Model\Order\ItemFactory $magentoSalesOrderItem,
        \Firas\GiftCard\Helper\Data $helperData,
        \Firas\GiftCard\Model\GiftDetailFactory $giftDetailFactory,
        \Firas\GiftCard\Model\GiftUserFactory $giftUserFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_salesOrder = $salesOrder;
        $this->_catalpgProduct = $catalpgProduct;
        $this->_magentoSalesRule = $magentoSalesRule;
        $this->_magentoSalesOrderItem = $magentoSalesOrderItem;
        $this->_helperData = $helperData;
        $this->_giftDetailFactory = $giftDetailFactory;
        $this->_giftUserFactory = $giftUserFactory;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
    }

/**
 * This is the method that fires when the event runs.
 *
 * @param Observer $observer
 */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $oids = $invoice->getOrderId();
        $sl = $this->_salesOrder->load($oids);
        $realOrderId = $sl->getIncrementId();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_Symbol = $_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($this->_helperData->getBaseCurrencyCode());
        $couponCode=$sl->getCouponCode();
        $discountAmt=$sl->getDiscountAmount();
        foreach ($invoice->getAllItems() as $item) {
            $productid = $item->getProductId();
            $gcqty=$item->getQty();
            for ($i=0; $i < intval($gcqty); $i++) {
                $giftmodel  = $this->_catalpgProduct->load($productid);
                if ($giftmodel->getTypeId() == 'giftcard') {
                    $userEmail = "";
                    $userMessage = "";
                    $options = $this->_magentoSalesOrderItem->create()->load($item->getOrderItemId())->getProductOptions();
                    $customOptions = $options['options'];
                    if (!empty($customOptions)) {
                        foreach ($customOptions as $option) {
                            if ($option['label'] == 'Email To') {
                                $userEmail = $option['value'];
                            }
                            if ($option['label'] == 'Message') {
                                $userMessage = $option['value'];
                            }
                        }
                    }
                    $customer=$sl->getCustomerEmail();
                    $customer_name=$sl->getCustomerFirstname()." ".$sl->getCustomerLastname();
                    $mailData=[];
                    /* Assign values for your template variables  */
                    $emailTemplateVariables = [];
                    // $price= $giftmodel->getPrice();
                    $price= $item->getPrice();
                    $mailData['price']=$price;
                    $emailTemplateVariables['myvar1'] = $price;
                    // $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    // $_Symbol = $_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($this->_helperData->getBaseCurrencyCode());
                    $emailTemplateVariables['myvar8'] = $_Symbol->getCurrencySymbol();
                    // $des= $giftmodel->getShortDescription();
                    $des = $giftmodel->getDescription();
                    $mailData['description']=$des;
                    $emailTemplateVariables['myvar2'] = $des;
                    $email = $userEmail;
                    $mailData['reciever']=$email;
                    /* Receiver Detail  */
                    $receiverInfo = [
                        'name' => 'Reciver Name',
                        'email' => $email
                    ];
                    $emailTemplateVariables['myvar6'] = 'Reciver Name';
                    $emailTemplateVariables['myvar7'] = $email;
                    $emailTemplateVariables['myvar9'] = $userMessage;

                    $giftcode=$this->_helperData->get_rand_id(12);
                    $mailData['sender']=$customer;
                    $mailData['sender_name']=$customer_name;
                    $emailTemplateVariables['myvar4'] = $customer;
                    $emailTemplateVariables['myvar5'] = $customer_name;
                    /* Sender Detail  */
                    $senderInfo = [
                        'name' => $customer_name,
                        'email' => $customer
                    ];
                    $usageDurationOfGiftCard = $this->_helperData->getGiftCardActiveDuration();
                    if ($email) {
                        $data=["price"=>$price,"description"=>$des,"email"=>$email,"from"=>$customer,"message"=>$userMessage,"duration"=>$usageDurationOfGiftCard,'order_id'=>$oids];
                        $model=$this->_giftDetailFactory->create()->setData($data);
                        $dateTimeAsTimeZone = $this->_timezoneInterface
                                        ->date(new \DateTime(date("Y/m/d h:i:sa")))
                                        ->format('Y/m/d H:i:s');
                        $emailTemplateVariables['myvar10'] = $this->_helperData->createExpirationDateOfGiftCard($usageDurationOfGiftCard, $dateTimeAsTimeZone);
                        $expiryDate = $this->_helperData->createExpirationDateOfGiftCard($usageDurationOfGiftCard, $dateTimeAsTimeZone);
                        $expiryDate = date('Y-m-d', strtotime(str_replace('.', '/', $expiryDate)));
                        try {
                            $id=$model->save()->getGiftId();
                            $model2=$this->_giftUserFactory->create()->setData(["giftcodeid"=>$id,"amount"=>$price,"alloted"=>$dateTimeAsTimeZone,"email"=>$email,"from"=>$customer,"remaining_amt"=>$price,"is_active"=>"yes","is_expire"=>0]);
                            $id2=$model2->save()->getGiftuserid();
                            $this->_giftDetailFactory->create()->load($id)->setGiftCode($id2.$giftcode)->save();
                            $this->_giftUserFactory->create()->load($id2)->setCode($id2.$giftcode)->save();
                            $emailTemplateVariables['myvar3'] = $id2.$giftcode;
                            $mailData['code']=$id2.$giftcode;
                            try {
                                $this->_helperData->customMailSendMethod(
                                    $emailTemplateVariables,
                                    $senderInfo,
                                    $receiverInfo
                                );
                            } catch (\Exception $e) {
                                $this->_messageManager->addError(__($e->getMessage()));
                                return false;
                            }
                        } catch (\Exception $e) {
                                $this->_messageManager->addError(__($e->getMessage()));
                                return false;
                        }
                    }
                    $giftAmount = intval($price);
                    // echo 'Customer Name:'.$customer.'<br/>'.$customer_name.'<br/>'.$userEmail.'<br/>'.$userMessage.'<br/>'.$giftcode.'<br/>'.$expiryDate.'<br/>'.$des.'<br/>'.$giftAmount;
                    //post gift card data using start
                    //API Url
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
                    $base = $directory->getRoot();
                    $path = $base . '/var/log/';
                    $date = date('Y-m-d');
                    $logFile = $realOrderId.'_'.$date.'-giftcard.log';

                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFile);
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);


                    $url = $this->getErpApiUrl().'giftcards/add';

                    //Initiate cURL.
                    $ch = curl_init($url);

                    //The JSON data.
                    $jsonData = array(
                        'sender_name' => $customer_name,
                        'sender_email' => $customer,
                        'receiver_name' => $userEmail,
                        'receiver_email' => $userEmail, //required, email
                        'gift_card_coupon_code' => $giftcode, //required, unique, upto 50 chars
                        'gift_card_description' => $des, //required, length maxminum 1000
                        'gift_card_amount' => $giftAmount, //required, integer
                        'gift_card_message' => $userMessage, //length maxminum 200
                        'expiry_date' => $expiryDate, //required, date after yesterday
                        'website' => "www.brands-labels.com" //required, must be a website in store websites
                    );

                    //Encode the array into JSON.
                    $jsonDataEncoded = json_encode($jsonData);

                    //Tell cURL that we want to send a POST request.
                    curl_setopt($ch, CURLOPT_POST, 1);

                    //Attach our encoded JSON string to the POST fields.
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

                    //Set the content type to application/json
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                    // Return response instead of outputting
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    //Execute the request
                    try{
                      $result = curl_exec($ch);
                      // $result = 'success';
                      $err = curl_error($ch);

                      curl_close($ch);

                      $logger->log(Zend\Log\Logger::INFO, $result.date('Y-m-d H:i:s'));
                    }
                    catch(Exception $e){
                      $logger->log(Zend\Log\Logger::INFO, $e.'=='.date('Y-m-d H:i:s'));
                    }
                    //API code end
                }
            }
        }
        $cc=$this->_giftUserFactory->create()->getCollection()->addFieldToFilter('code', $couponCode);
        if ($cc->getSize()) {
            $gift_user_data=[];
            $customerName=$sl->getCustomerFirstname()." ".$sl->getCustomerLastname();
            $customerEmail=$sl->getCustomerEmail();
            $gift_user_data["orderId"]=$sl->getIncrementId();
            $gift_user_data["reciever_email"]=$customerEmail;
            $gift_user_data["reciever_name"]=$customerName;
            $gift_user_data["reduced_ammount"]=$discountAmt;
            $emailTemplateVariablesForLeftAmt["myvar1"]=$sl->getIncrementId();
            $emailTemplateVariablesForLeftAmt["myvar2"]=$customerEmail;
            $emailTemplateVariablesForLeftAmt["myvar3"]=$customerName;
            $emailTemplateVariablesForLeftAmt["myvar4"]=$discountAmt;
            $emailTemplateVariablesForLeftAmt['myvar8'] = $_Symbol->getCurrencySymbol();
            $model3=$this->_giftUserFactory->create()
            ->getCollection()
            ->addFieldToFilter("code", $couponCode);
            foreach ($model3 as $m3) {
                $gift_user_data["previous_ammount"]=$amnt=$m3->getAmount();
                $gift_user_data["gift_code"]=$m3->getCode();
                $emailTemplateVariablesForLeftAmt["myvar5"]=$amnt=$m3->getAmount();
                $emailTemplateVariablesForLeftAmt["myvar6"]=$m3->getCode();
                $m3->setAmount($amnt+$discountAmt)->save();
                $gift_user_data["result_ammount"]=$m3->getAmount();
                $emailTemplateVariablesForLeftAmt["myvar7"]=$m3->getAmount();
                $giftCodeId = $m3->getGiftcodeid();
                $date = $m3->getAlloted();
            }
            $giftDetailModel = $this->_giftDetailFactory->create()->load($giftCodeId);
            $duration = $giftDetailModel->getDuration();
            $emailTemplateVariablesForLeftAmt["myvar9"] = $date;
            $emailTemplateVariablesForLeftAmt["myvar10"] = $this->_helperData->createExpirationDateOfGiftCard($duration, $date);
            $collection = $this->_magentoSalesRule->getCollection()->load();
            foreach ($collection as $m) {
                if ($m->getName() == $couponCode) {
                    $m->delete();
                }
            }
            $receiverInfo = [
                'name' => $customerName,
                'email' => $customerEmail
            ];
            $adminName = $this->_helperData->getAdminNameFromConfig();
            $adminEmail = $this->_helperData->getAdminEmailFromConfig();
            if (!isset($adminName) || $adminName == "") {
                $adminName = $this->_helperData->getStorename();
            }
            if (!isset($adminEmail) || $adminEmail == "") {
                $adminEmail = $this->_helperData->getStoreEmail();
            }
            $senderInfo = [
                'name' => $adminName,
                'email' => $adminEmail
            ];
            $emailTemplateVariablesForLeftAmt['myvar8'] = $this->_helperData->getBaseCurrencyCode();
            $this->_helperData->customMailSendMethodForLeftAmt(
                $emailTemplateVariablesForLeftAmt,
                $senderInfo,
                $receiverInfo
            );
            $coupon_model = $this->_magentoSalesRule->getCollection()->load();
            foreach ($coupon_model as $cpn) {
                if (trim($cpn->getName()) == trim($couponCode)) {
                    $cpn->delete();
                }
            }
        }
    }
    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getErpApiUrl()
    {
        return $this->getConfig(self::XML_PATH_ERP_API_URL);
    }
}
