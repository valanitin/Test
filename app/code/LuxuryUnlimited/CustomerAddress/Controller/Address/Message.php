<?php

declare(strict_types=1);

namespace LuxuryUnlimited\CustomerAddress\Controller\Address;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * class Message
 */
class Message extends \Magento\Customer\Controller\Address implements HttpGetActionInterface
{
    /**
     * Address book form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('customer/address');
        }
        return $resultPage;
    }
}
