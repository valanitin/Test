<?php

declare(strict_types=1);

namespace LuxuryUnlimited\OrderApi\Plugin\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param $result
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterGetList(\Magento\Sales\Api\OrderRepositoryInterface $subject, $result, SearchCriteriaInterface $searchCriteria)
    {
        foreach ($result->getItems() as $order) {
            $currencyCode = $order->getOrderCurrencyCode();
            $order->setBaseGrandTotal($this->formatProductPrice($currencyCode, $order->getBaseGrandTotal()));
            $order->setBaseSubtotalInclTax($this->formatProductPrice($currencyCode, $order->getBaseSubtotalInclTax()));
            $order->setBaseSubtotal($this->formatProductPrice($currencyCode, $order->getBaseSubtotal()));
            $order->setBaseDiscountAmount($this->formatProductPrice($currencyCode, $order->getBaseDiscountAmount()));
            $order->setBaseDiscountTaxCompensationAmount($this->formatProductPrice($currencyCode, $order->getBaseDiscountTaxCompensationAmount()));
            $order->setBaseShippingAmount($this->formatProductPrice($currencyCode, $order->getBaseShippingAmount()));
            $order->setBaseShippingDiscountAmount($this->formatProductPrice($currencyCode, $order->getBaseShippingDiscountAmount()));
            $order->setBaseShippingDiscountTaxCompensationAmnt($this->formatProductPrice($currencyCode, $order->getBaseShippingDiscountTaxCompensationAmnt()));
            $order->setBaseShippingInclTax($this->formatProductPrice($currencyCode, $order->getBaseShippingInclTax()));
            $order->setBaseShippingTaxAmount($this->formatProductPrice($currencyCode, $order->getBaseShippingTaxAmount()));
            $order->setBaseTaxAmount($this->formatProductPrice($currencyCode, $order->getBaseTaxAmount()));

            foreach ($order->getItems() as $product) {
                $product->setBaseAmountRefunded($this->formatProductPrice($currencyCode, $product->getBaseAmountRefunded()));
                $product->setBaseDiscountAmount($this->formatProductPrice($currencyCode, $product->getBaseDiscountAmount()));
                $product->setBaseDiscountInvoiced($this->formatProductPrice($currencyCode, $product->getBaseDiscountInvoiced()));
                $product->setBaseDiscountTaxCompensationAmount($this->formatProductPrice($currencyCode, $product->getBaseDiscountTaxCompensationAmount()));
                $product->setBaseOriginalPrice($this->formatProductPrice($currencyCode, $product->getBaseOriginalPrice()));
                $product->setBasePrice($this->formatProductPrice($currencyCode, $product->getBasePrice()));
                $product->setBasePriceInclTax($this->formatProductPrice($currencyCode, $product->getBasePriceInclTax()));
                $product->setBaseRowInvoiced($this->formatProductPrice($currencyCode, $product->getBaseRowInvoiced()));
                $product->setBaseRowTotal($this->formatProductPrice($currencyCode, $product->getBaseRowTotal()));
                $product->setBaseRowTotalInclTax($this->formatProductPrice($currencyCode, $product->getBaseRowTotalInclTax()));
                $product->setBaseTaxAmount($this->formatProductPrice($currencyCode, $product->getBaseTaxAmount()));
                $product->setBaseTaxInvoiced($this->formatProductPrice($currencyCode, $product->getBaseTaxInvoiced()));
            }
        }
        return $result;
    }

    /**
     * @param $currencyCodeTo
     * @param $price
     * @return float
     * @throws NoSuchEntityException
     */
    public function formatProductPrice($currencyCodeTo, $price): float
    {
        $currency = $this->storeManager->getStore()->getBaseCurrency();
        return $currency->convert($price, $currencyCodeTo);
    }
}