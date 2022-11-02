<?php

declare(strict_types=1);

namespace LuxuryUnlimited\CustomerAddress\Plugin\Address;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Address\Index;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;

/**
 * class Plugin
 */
class IndexPlugin
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Context $context
     */
    public function __construct(
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Context $context
    ){
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * @param Index $subject
     * @param \Closure $proceed
     * @return \Magento\Backend\Model\View\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundExecute(Index $subject, \Closure $proceed) {
        $addresses = $this->customerRepository->getById($this->customerSession->getCustomer()->getId())->getAddresses();
        if (count($addresses)) {
            return $proceed();
        } else {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/message');
        }
    }
}