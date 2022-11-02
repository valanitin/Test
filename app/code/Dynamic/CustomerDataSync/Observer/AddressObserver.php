<?php

namespace Dynamic\CustomerDataSync\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddressObserver implements ObserverInterface
{
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->customerFactory = $customerFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();
        $customer = $this->customerFactory->create()->load($customer->getId());
        $customerDataModel = $customer->getDataModel();
        $customerDataModel->setCustomAttribute("address_sync", 1);
        $customer->updateData($customerDataModel);
        $customer->save();
    }
}
