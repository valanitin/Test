<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Controller\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Checkout\Model\Cart;
use Magento\SalesRule\Model\CouponFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use LuxuryUnlimited\Coupon\Helper\Validator as CouponValidator;
use LuxuryUnlimited\Coupon\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use LuxuryUnlimited\Coupon\Model\SalesRuleLoggingFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost
{
    /**
     * Sales quote repository
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var CouponFactory
     */
    protected $couponFactory;
    
    /**
     * @var CouponValidator
     */
    protected $couponValidator;
    
    /**
     * @var Data
     */
    protected $configData;

    /**
     * @var SalesRuleLoggingFactory
     */
    protected $salesRuleLogFactory;

    /**
     * @var TimezoneInterface
     */
    protected $date;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \LuxuryUnlimited\Coupon\Helper\Validator $couponValidator
     * @param \LuxuryUnlimited\Coupon\Helper\Data $configData
     * @param \LuxuryUnlimited\Coupon\Model\SalesRuleLoggingFactory $salesRuleLogFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        Cart $cart,
        CouponFactory $couponFactory,
        CartRepositoryInterface $quoteRepository,
        CouponValidator $couponValidator,
        Data $configData,
		ResultFactory $resultFactory,
        SalesRuleLoggingFactory $salesRuleLogFactory,
        TimezoneInterface $date
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $couponFactory,
            $quoteRepository
        );
        $this->couponValidator = $couponValidator;
        $this->configData = $configData;
		$this->resultFactory = $resultFactory;
        $this->salesRuleLogFactory = $salesRuleLogFactory;
        $this->date = $date;
    }
   
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$status = $msg = '';
        $salesRuleLog = $this->salesRuleLogFactory->create();
		if ($this->getRequest()->isPost() && $this->getRequest()->isAjax()) {
			$response = $this->couponAjaxCheck();			
			return $resultJson->setData($response);			
		}
		
		$couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
               
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $discountAmount = $cartQuote->getSubtotal() - $cartQuote->getSubtotalWithDiscount();
                        $successMessage = $this->configData->getSuccessMessage($couponCode,$discountAmount);
                        $this->messageManager->addSuccessMessage(
                            __('%1',$successMessage)
                        );
						
                    } else {
                        if ($this->configData->isEnabled()) {
                            $msg=$this->couponValidator->validate($couponCode);
                            $this->messageManager->addErrorMessage(
                               __('%1', $msg)
                            );
							
                        } else {
                            $this->messageManager->addErrorMessage(
                                __(
                                   'The coupon code "%1" is not valid.',
                                    $escaper->escapeHtml($couponCode)
                               )
                            );					
                        }
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $discountAmount = $cartQuote->getSubtotal() - $cartQuote->getSubtotalWithDiscount();
                        $successMessage = $this->configData->getSuccessMessage($couponCode,$discountAmount);
                        $this->messageManager->addSuccessMessage(
                            __('%1',$successMessage)
                        );
						
                    } else {
                        if ($this->configData->isEnabled()) {
                            $msg=$this->couponValidator->validate($couponCode);
                            $this->messageManager->addErrorMessage(
                                __('%1', $msg)
                            );
							
                        } else {
                            $this->messageManager->addErrorMessage(
                                __(
                                    'The coupon code "%1" is not valid.',
                                    $escaper->escapeHtml($couponCode)
                                )
                            );
						
                        }
                    }
                }
            } else {
                $cancelMessage = $this->configData->getCancelMessage($couponCode);
                $this->messageManager->addSuccessMessage(__('%1',$cancelMessage));
				
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
			
        } catch (\Exception $e) {
            $exceptionMessage = $this->configData->getExceptionMessage($couponCode);
            $this->messageManager->addErrorMessage(__('%1',$exceptionMessage));
			
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
		
        return $this->_goBack();
    }
	
	public function couponAjaxCheck()
	{
		$status = $msg = '';
		$couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();
        $salesRuleLog = $this->salesRuleLogFactory->create();
        $codeLength = strlen($couponCode);

        if (!$codeLength && !strlen($oldCouponCode)) {
            return $response = [
				'status' => 'error',
				'message' => 'please enter the coupon code'
			];
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                $subtotal = $cartQuote->getBaseSubtotal();
                $subtotalWithDiscount = $cartQuote->getBaseSubtotalWithDiscount();
                $discount = $subtotal - $subtotalWithDiscount;

                $data = [
                    "date" => $this->date->date()->format('Y-m-d H:i:s'),
                    "store_id" => $cartQuote->getStoreId(),
                    "coupon_code" => $couponCode,
                    "customer_id" => $cartQuote->getCustomerId(),
                    "coupon_id" => $coupon->loadByCode($couponCode)->getCouponId(),
                    "subtotal_amount" => $subtotal,
                    "discount_amount" => $discount,
                    "total_amount" => $cartQuote->getBaseGrandTotal(),
                    "quote_id" => $cartQuote->getId(),
                    "rule_id" => $coupon->loadByCode($couponCode)->getRuleId()
                ];

                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $discountAmount = $cartQuote->getSubtotal() - $cartQuote->getSubtotalWithDiscount();
                        $successMessage = $this->configData->getSuccessMessage($couponCode,$discountAmount);
						$status = 'success';
						$msg = __('%1',$successMessage);
                        $data["status"] = $status;
                        $data["validation"] = $msg;
                        $salesRuleLog->setData($data)->save();
                    } else {
                        if ($this->configData->isEnabled()) {
                            $msg=$this->couponValidator->validate($couponCode);
							$status = 'error';
							$msg = __('%1',$msg);    
                            $data["status"] = $status;
                            $data["validation"] = $msg;
                            $salesRuleLog->setData($data)->save();
                        } else {
							$status = 'error';
							$msg = __(
                                    'The coupon code "%1" is not valid.',
                                    $escaper->escapeHtml($couponCode)
                                );
                            $data["status"] = $status;
                            $data["validation"] = $msg;
                            $salesRuleLog->setData($data)->save();
                        }
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $discountAmount = $cartQuote->getSubtotal() - $cartQuote->getSubtotalWithDiscount();
                        $successMessage = $this->configData->getSuccessMessage($couponCode,$discountAmount);
						$status = 'success';
						$msg = __('%1',$successMessage);
                        $data["status"] = $status;
                        $data["validation"] = $msg;
                        $salesRuleLog->setData($data)->save();
                    } else {
                        if ($this->configData->isEnabled()) {
                            $msg=$this->couponValidator->validate($couponCode);
                           
							$status = 'error';
							$msg = __('%1', $msg);                            
                            $data["status"] = $status;
                            $data["validation"] = $msg;
                            $salesRuleLog->setData($data)->save();
                        } else {
                            
							$status = 'error';
							$msg = __(
                                    'The coupon code "%1" is not valid.',
                                    $escaper->escapeHtml($couponCode)
                                );                            
                            $data["status"] = $status;
                            $data["validation"] = $msg;
                            $salesRuleLog->setData($data)->save();
                        }
                    }
                }
            } else {
                $coupon = $this->couponFactory->create();
                $subtotal = $cartQuote->getBaseSubtotal();
                $subtotalWithDiscount = $cartQuote->getBaseSubtotalWithDiscount();
                $discount = $subtotal - $subtotalWithDiscount;
                $oldCoupon = $this->getRequest()->getParam('coupon_code');
                $data = [
                    "date" => $this->date->date()->format('Y-m-d H:i:s'),
                    "store_id" => $cartQuote->getStoreId(),
                    "coupon_code" => $oldCoupon,
                    "customer_id" => $cartQuote->getCustomerId(),
                    "coupon_id" => $coupon->loadByCode($oldCoupon)->getCouponId(),
                    "subtotal_amount" => $subtotal,
                    "discount_amount" => $discount,
                    "total_amount" => $cartQuote->getBaseGrandTotal(),
                    "quote_id" => $cartQuote->getId(),
                    "rule_id" => $coupon->loadByCode($oldCoupon)->getRuleId()
                ];
                $cancelMessage = $this->configData->getCancelMessage($couponCode);
				$status = 'success';
				$msg = __('%1',$cancelMessage);            
                $data["status"] = "cancelled";
                $data["validation"] = $msg;
                $salesRuleLog->setData($data)->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            
			$status = 'error';
			$msg = __('%1',$e->getMessage());
            $data["status"] = $status;
            $data["validation"] = $msg;
            $salesRuleLog->setData($data)->save();
        } catch (\Exception $e) {
            $exceptionMessage = $this->configData->getExceptionMessage($couponCode);
            
			$status = 'error';
			$msg = __('%1',$exceptionMessage);
            $data["status"] = $status;
            $data["validation"] = $msg;
            $salesRuleLog->setData($data)->save();
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
		return $response = [
			'status' => $status,
			'message' => $msg
		];
	}
}