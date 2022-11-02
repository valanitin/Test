<?php

declare(strict_types=1);

namespace LuxuryUnlimited\RequestTicket\Block;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Block\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * class View
 */
class View extends Template
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Data
     */
    protected $directoryBlock;
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param HttpContext $httpContext
     * @param Data $directoryBlock
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepositoryInterface,
        HttpContext $httpContext,
        Data $directoryBlock,
        array $data = []
    )
    {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->httpContext = $httpContext;
        $this->directoryBlock = $directoryBlock;
        parent::__construct($context, $data);
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