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

class AfterPlaceOrder implements ObserverInterface
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesOrder;

    /**
     * @var \Firas\GiftCard\Model\GiftUserFactory
     */
    protected $_giftUserFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_salesRule;

    /**
     * Constructor.
     *
     * @param \Magento\Sales\Model\Order                         $salesOrder
     * @param \Firas\GiftCard\Model\GiftUserFactory            $giftUserFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\SalesRule\Model\Rule                      $salesRule
     */
    public function __construct(
        \Magento\Sales\Model\Order $salesOrder,
        \Firas\GiftCard\Model\GiftUserFactory $giftUserFactory,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\SalesRule\Model\Rule $salesRule
    ) {
        $this->_salesOrder = $salesOrder;
        $this->_giftUserFactory = $giftUserFactory;
        $this->_session = $session;
        $this->_customerSession = $customerSession;
        $this->_salesRule = $salesRule;
    }

    /**
     * This is the method that fires when the event runs.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $oids=$observer->getOrderIds();
        $sl = $this->_salesOrder->load($oids);
            $rpr = $this->_session->getReducedprice();
        if (!empty($rpr)) {
            $gift_user_data=[];
            $customerEmail=$this->_customerSession->getCustomer()->getEmail();
            $customerName=$this->_customerSession->getCustomer()->getName();
            $gift_user_data["orderId"]=$sl->getIncrementId();
            $gift_user_data["reciever_email"]=$customerEmail;
            $gift_user_data["reciever_name"]=$customerName;
            $gift_user_data["reduced_ammount"]=$this->_session->getReducedprice();
            $model3=$this->_giftUserFactory->create()
                ->getCollection()
                ->addFieldToFilter("code", $this->_session->getCoupancode())
                ->addFieldToFilter("email", $customerEmail);
            foreach ($model3 as $m3) {
                $amnt=$m3->getRemainingAmt();
                $m3->setRemainingAmt($amnt-$this->_session->getReducedprice())->save();
            }
            $collection = $this->_salesRule->getCollection()->load();
            foreach ($collection as $m) {
                if ($m->getName() == $this->_session->getCoupancode()) {
                    $m->delete();
                }
            }
            $coupon_model = $this->_salesRule->getCollection()->load();
            foreach ($coupon_model as $cpn) {
                if (trim($cpn->getName()) == trim($this->_session->getCoupancode())) {
                    $cpn->delete();
                }
            }
            $this->_session->setReducedprice(null);
            $this->_session->setCoupancode(null);
        }
    }
}
