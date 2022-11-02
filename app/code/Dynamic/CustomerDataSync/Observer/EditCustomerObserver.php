<?php

declare (strict_types = 1);

namespace Dynamic\CustomerDataSync\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class EditCustomerObserver implements ObserverInterface
{
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Storemanager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storemanager;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storemanager
    ) {
        $this->customerFactory = $customerFactory;
        $this->storemanager = $storemanager;
    }

    public function execute(Observer $observer)
    {
        $customerEmail = $observer->getEvent()->getEmail();
        $websiteId = $this->storemanager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($customerEmail);
        $customerDataModel = $customer->getDataModel();
        $customerDataModel->setCustomAttribute("account_sync", 1);
        $customer->updateData($customerDataModel);
        $customer->save();
    }
}