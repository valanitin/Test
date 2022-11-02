<?php

declare(strict_types=1);

namespace Sololuxary\PriceMatch\ViewModel;
 
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Block\Data;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * class PriceMatch
 */
class PriceMatch implements ArgumentInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var Data
     */
    protected $directoryBlock;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Context $httpContext
     * @param Data $directoryBlock
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        Context $httpContext,
        Data $directoryBlock
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->httpContext = $httpContext;
        $this->directoryBlock = $directoryBlock;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return mixed|null
     */
    public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerById($customerId) {
        return $this->_customerRepositoryInterface->getById($customerId);
    }

    /**
     * @param $address
     * @return null
     */
    public function getTelephone($address) {
        foreach ($address as $oneAddress) {
            if ($oneAddress->getTelephone()) {
                return $oneAddress->getTelephone();
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getCountries(): string
    {
        return $this->directoryBlock->getCountryHtmlSelect();
    }
}
