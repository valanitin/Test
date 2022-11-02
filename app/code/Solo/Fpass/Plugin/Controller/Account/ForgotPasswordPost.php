<?php

namespace Solo\Fpass\Plugin\Controller\Account;

/**
 * Plugin of Post login customer action.
 */
class ForgotPasswordPost
{




    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\ForgotPasswordPost $subject,
        $result
    )
    {
        //var_dump();
        //$this->customerSession->setMyEmailId($subject->getRequest()->getParam('email'));


           $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $coreSession = $objectManager->get('Magento\Framework\Session\SessionManagerInterface'); 
    $coreSession->start();
    // Set Session
    $coreSession->setData('femail', $subject->getRequest()->getParam('email'));
    //var_dump($coreSession->getData('femail'));
    //die;

        $result->setPath('forget'); // Set path where you want to redirect
        return $result;
    }
}