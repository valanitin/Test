<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\NewsLetterSubscription\Controller\Subscriber;

use Magento\Framework\App\ObjectManager;

class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /**
     * @var \Magento\Framework\Controller\Result\Json
     */
    protected $_resultJson;

    /**
     * New subscription action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {
        $result = [];
        $result['error'] = true;
        $result['message'] = __(' ');
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && $subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
                ) {
                    $result['message'] = __('This email address is already subscribed');
                } else {
                    $status = $this->_subscriberFactory->create()->subscribe($email);
                    if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                        $result['message'] = __('The confirmation request has been sent.');
                        $result['error'] = false;
                    } else {
                        $result['message'] = __("You've been added to our mailing list.");
                        $result['error'] = false;
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result['message'] = __('Try after some time');
            } catch (\Exception $e) {
                $result['message'] = __('Try after some time');
            }
        }
        return $this->getResultJson()->setData($result);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function getResultJson()
    {
        if ($this->_resultJson === null) {
            $this->_resultJson = ObjectManager::getInstance()->get(\Magento\Framework\Controller\Result\Json::class);
        }
        return $this->_resultJson;
    }
}