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
 
class CartLoadBefore implements ObserverInterface
{
 
    /** @var \Magento\Framework\Logger\Monolog */
    protected $_logger;

    /** @var Magento\Framework\App\RequestInterface */
    protected $_request;

    /**
     * @param \Psr\Log\LoggerInterface               $loggerInterface
     * @param RequestInterface                       $requestInterface
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        RequestInterface $requestInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Firas\GiftCard\Model\GiftUserFactory $giftUser,
        \Firas\GiftCard\Helper\Data $dataHelper,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Checkout\Model\Cart $quote,
        \Magento\Framework\Message\ManagerInterface $messegeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->_logger = $loggerInterface;
        $this->_request = $requestInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_giftUser = $giftUser;
        $this->_dataHelper = $dataHelper;
        $this->_session = $session;
        $this->_quote = $quote;
        $this->_messegeManager = $messegeManager;
        $this->_url = $url;
        $this->_responseFactory = $responseFactory;
    }
    /**
     * This is the method that fires when the event runs.
     *
     * @param Observer $observer
     */

    public function execute(Observer $observer)
    {
        // $customeremail = $this->_customerSession->getCustomer()->getEmail();
        // $giftuser = $this->_giftUser->create()->getCollection();

        // $discountAmount=0;
        // $flag=0;
        // $flag1=0;
        // $coupon_code = $this->_checkoutSession->getQuote()->getCouponCode();

        // $quote = $this->_checkoutSession->getQuote();
        // foreach ($quote->getAllItems() as $item){
        //  $discountAmount+=(real)$item->getDiscountAmount();
        // }
        // $baseCurrencyCode = $this->_dataHelper->getBaseCurrencyCode();
        // $currentCurrencyCode = $this->_dataHelper->getCurrentCurrencyCode();
        // $allowedCurrencies = $this->_dataHelper->getAllowedCurrencies();
        // $rates = $this->_dataHelper->getCurrentCurrencyRate();
        // $discountAmount = $discountAmount*$rates;
        // // $discountAmount = $discountAmount*$rates[$baseCurrencyCode];
        // if($coupon_code)
        // {
        //             foreach($giftuser as $code)
        //             {
        //                 if(trim($code->getCode()) == trim($coupon_code))
        //                 {
        //                     $flag1=1;
                            
        //                     if(trim($customeremail) == trim($code->getEmail()))
        //                     {
        //                         $this->_session->setReducedprice((real)$discountAmount);
        //                         $this->_session->setCouponcode($coupon_code);
        //                         $flag=1;
        //                     }
        //                 }
        //             }
        //     if($flag==0 && $flag1==1)
        //     {
        //         $this->_quote->getQuote()->setData('coupon_code','')->save();
        //         $CustomRedirectionUrl = $this->_url->getUrl('checkout/cart');
        //         $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
        //         // Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"));
        //         $this->_messageManager->addError(__('Coupon code is invalid , discount not allowed'));
        //         // Mage::getSingleton('checkout/session')->addError(Mage::helper('giftcard')->__('Coupon code is invalid , discount not allowed'));
        //     }
        // }
    }
}
