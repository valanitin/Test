<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Controller\GiftCard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Firas GiftCard Landing page UpdateGiftCard Controller.
 */
class UpdateGiftCard extends Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Firas\GiftCard\Model\GiftUserFactory
     */
    protected $_giftuser;
    
    /**
     * @var \Firas\GiftCard\Model\GiftDetailFactory
     */
    protected $_giftDetail;
    
    /**
     * @var \Firas\GiftCard\Helper\Data
     */
    protected $_dataHelper;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_salesRule;
    
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_quote;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_backendSession;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Firas\GiftCard\Model\GiftUserFactory $giftUser
     * @param \Firas\GiftCard\Model\GiftDetailFactory $giftDetail
     * @param \Firas\GiftCard\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @param \Magento\Checkout\Model\Cart $quote
     * @param \Magento\Framework\Session\SessionManagerInterface $backendSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Firas\GiftCard\Model\GiftUserFactory $giftUser,
        \Firas\GiftCard\Model\GiftDetailFactory $giftDetail,
        \Firas\GiftCard\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\SalesRule\Model\Rule $salesRule,
        \Magento\Checkout\Model\Cart $quote,
        \Magento\Framework\Session\SessionManagerInterface $backendSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_storeManager = $storeManager;
        $this->_giftuser = $giftUser;
        $this->_giftDetail = $giftDetail;
        $this->_dataHelper = $dataHelper;
        $this->_customerSession = $customerSession;
        $this->_salesRule = $salesRule;
        $this->_quote = $quote;
        $this->_backendSession = $backendSession;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $param=$this->getRequest()->getParams();
        $baseCurrencyCode = $this->_dataHelper->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_dataHelper->getCurrentCurrencyCode();
        $allowedCurrencies = $this->_dataHelper->getAllowedCurrencies();
        $rates = $this->_dataHelper->getCurrentCurrencyRate();
        $price = $param['amount'];
        $price=$price/$rates;
        $param['amount']=$price;
        if ((real)$param['amount']>0) {
            $whom="";
            $collections=$this->_giftuser->create()->getCollection();
            $model=$collections->addFieldToFilter("code", $param["code"]);
            if ($model->getSize()) {
                $duration = $this->_giftDetail->create()->load($model->getColumnValues('giftcodeid')[0])->getDuration();
                $isExpire = $this->_dataHelper->checkExpirationOfGiftCard($model->getColumnValues('alloted')[0], $duration);
                if ($isExpire) {
                    foreach ($model as $giftUserModel) {
                        $giftUserModel->setIsExpire(1);
                        $giftUserModel->save();
                    }
                    $this->messageManager->addError(__("The gift code %1 is expired", trim($param['code'])));
                    return false;
                }
                if ($model->getColumnValues('is_active')[0] != "yes") {
                    $this->messageManager->addError(__("The gift code %1 is disable.Please contact administration.", trim($param['code'])));
                    return false;
                }
                foreach ($model as $m) {
                    $whom=$m->getEmail();
                }
                $customerEmail=$this->_customerSession->getCustomer()->getEmail();
                // if($whom!=$customerEmail) {
                //         $collection = Mage::getModel('salesrule/rule')->getCollection()->load();
                //         foreach($collection as $mo) {
                //                 // Delete coupon
                //             if ($mo->getName() == $session->getData("couponcode") )  {
                //                 $mo->delete();
                //                 $session->unsetData("couponcode");
                //                 $session->unsetData("reducedprice");
                //             }
                //         }
                //         echo Mage::helper('giftcard')->__("Gift code is incorrect or you are not authorized to use this gift card,please check the code !!");
                //         exit(0);
                // }
                $usermodel=$collections->addFieldToFilter("email", $customerEmail)->addFieldToFilter("code", trim($param['code']));
                $acamm=0;
                if ($usermodel->getSize() > 0) {
                    foreach ($usermodel as $u) {
                        $acamm=(real)$u->getRemainingAmt();
                    }
                    if ((real)$param['amount']>$acamm) {
                        $param['amount']=$acamm;
                    }
                }
                if ((real)$param['amount']==0) {
                    $collection = $this->_salesRule->getCollection();
                    foreach ($collection as $mo) {
                        // Delete coupon
                        if ($mo->getName() == trim($param['code'])) {
                            $mo->delete();
                            $this->_backendSession->setCoupancode(null);
                            $this->_backendSession->setReduceprice(null);
                            // $session->unsetData("couponcode");
                            // $session->unsetData("reducedprice");
                        }
                    }
                    $this->messageManager->addError(__("Gift code has been expired."));
                } elseif ((real)$param['amount']<=$acamm) {
                    if (!empty($param['code'])) {
                            $model=$collections->addFieldToFilter("code", trim($param['code']));
                        foreach ($model as $m) {
                            $giftcode=$m->getCode();
                        }
                        if ($giftcode==trim($param['code'])) {
                            $this->_backendSession->setReducedprice((real)$param['amount']);
                            $this->_backendSession->setCoupancode(trim($param['code']));
                            $name = trim($param['code']);
                            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
                            $storeId = $this->_storeManager->getStore()->getStoreId();
                            $customerGroupId = 1;
                            $actionType = 'cart_fixed';
                            $baseCurrencyCode = $this->_dataHelper->getBaseCurrencyCode();
                            $currentCurrencyCode = $this->_dataHelper->getCurrentCurrencyCode();
                            $allowedCurrencies = $this->_dataHelper->getAllowedCurrencies();
                            $rates = $this->_dataHelper->getCurrentCurrencyRate();
                            $discount = (real)$param['amount']*$rates;
                            $shoppingCartPriceRule = $this->_salesRule;
                            $collection = $this->_salesRule->getCollection();
                            foreach ($collection as $model) {
                                if ($model->getName() == trim($param['code'])) {
                                    $model->delete();
                                }
                            }
                            $shoppingCartPriceRule
                            ->setName($name)
                            ->setCouponCode($name)
                            ->setDescription('')
                            ->setIsActive(1)
                            ->setWebsiteIds([$websiteId])
                            ->setCustomerGroupIds([$customerGroupId])
                            ->setFromDate('')
                            ->setCouponType(2)
                            ->setToDate('')
                            ->setSortOrder('')
                            ->setSimpleAction($actionType)
                            ->setDiscountAmount($discount)
                            ->setStopRulesProcessing(0);
                            try {
                                $shoppingCartPriceRule->save();
                            } catch (\Exception $e) {
                                $this->messageManager->addError(__($e->getMessage()));
                                return;
                            }
                            $this->_quote
                            ->getQuote()
                            ->setCouponCode(trim($param['code']))
                            ->collectTotals()
                            ->save();
                            $this->_checkoutSession->setCartWasUpdated(true);
                            $price = $param['amount'];
                            $param['amount']=$price*$rates;
                            $this->messageManager->addSuccess(__('Gift Card Discount Applied Successfully'));
                            if ($param['amount']==0) {
                                $this->_backendSession->setCoupancode(null);
                            }
                        } else {
                            $collection = $this->_salesRule->getCollection()->load();
                            foreach ($collection as $model) {
                                // Delete coupon
                                if ($model->getName() == trim($param['code'])) {
                                    $model->delete();
                                }
                            }
                            $this->messageManager->addError(__("The gift code %1 is not valid", trim($param['code'])));
                        }
                    } else {
                        $collection = $this->_salesRule->getCollection()->load();
                        foreach ($collection as $model) {
                            // Delete coupon
                            if ($model->getName() == trim($param['code'])) {
                                $model->delete();
                            }
                        }
                        $this->messageManager->addError(__("code is required"));
                    }
                } else {
                    $collection = $this->_salesRule->getCollection()->load();
                    foreach ($collection as $mo) {
                        // Delete coupon
                        if ($mo->getName() == trim($param['code'])) {
                                $mo->delete();
                                $this->_backendSession->setCoupancode(null);
                                $this->_backendSession->setReduceprice(null);
                        }
                    }
                    $this->messageManager->addError(__("Please enter a valid amount"));
                }
            } else {
                $this->messageManager->addError(__("The gift code %1 is not valid", trim($param['code'])));
            }
        } else {
            $collection = $this->_salesRule->getCollection()->load();
            foreach ($collection as $mo) {
                // Delete coupon
                if ($mo->getName() == trim($param['code'])) {
                    $mo->delete();
                    $this->_backendSession->setCoupancode(null);
                    $this->_backendSession->setReduceprice(null);
                }
            }
            $this->messageManager->addError(__("Please enter a valid amount"));
        }
    }
}
