<?php

declare(strict_types=1);

namespace LuxuryUnlimited\QuoteApi\Plugin\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Request;

/**
 * class QuoteManagementPlugin
 */
class QuoteManagementPlugin
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Request $request
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param $result
     * @param $customerId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCartForCustomer(\Magento\Quote\Model\QuoteManagement $subject, $result, $customerId)
    {
        $currencyCode = $this->request->getParam('currency');
        $items = $result->getItems();
        foreach ($items as $item) {
            $itemPrice = $item->getPrice();
            if($currencyCode) {
                $currencyCodeTo = $currencyCode;
            } else {
                $currencyCodeTo = $result->getQuoteCurrencyCode();
            }
            $item->setPrice($this->formatProductPrice($currencyCodeTo, $itemPrice));
        }
        return $result;
    }

    /**
     * @param $currencyCodeTo
     * @param $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function formatProductPrice($currencyCodeTo, $price): float
    {
        $currency = $this->storeManager->getStore()->getBaseCurrency();
        return $currency->convert($price, $currencyCodeTo);
    }
}