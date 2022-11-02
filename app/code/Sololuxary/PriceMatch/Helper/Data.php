<?php

declare(strict_types=1);

namespace Sololuxary\PriceMatch\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * class Data
 */
class Data extends AbstractHelper
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
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param HttpContext $httpContext
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepositoryInterface,
        HttpContext $httpContext
    )
    {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->httpContext = $httpContext;
        parent::__construct($context);
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
}
