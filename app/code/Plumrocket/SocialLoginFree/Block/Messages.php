<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;

class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Framework\Message\NoticeFactory
     */
    protected $noticeFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * Messages constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context         $context
     * @param \Magento\Framework\Message\Factory                       $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory             $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface              $messageManager
     * @param InterpretationStrategyInterface                          $interpretationStrategy
     * @param \Plumrocket\SocialLoginFree\Helper\Data                  $helper
     * @param \Magento\Customer\Model\Session                          $customerSession
     * @param \Magento\Store\Model\Store                               $store
     * @param \Magento\Framework\Message\NoticeFactory                 $noticeFactory
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail $fakeEmail
     * @param array                                                    $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        \Plumrocket\SocialLoginFree\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Message\NoticeFactory $noticeFactory,
        FakeEmail $fakeEmail,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );

        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->store = $store;
        $this->noticeFactory = $noticeFactory;
        $this->fakeEmail = $fakeEmail;
    }

    protected function _prepareLayout()
    {
        if ($this->helper->moduleEnabled()) {
            $this->_fakeEmailMessage();
            $this->addMessages($this->messageManager->getMessages(true));
        }
        return parent::_prepareLayout();
    }

    protected function _fakeEmailMessage()
    {
        // Check email.
        $requestString = $this->_request->getRequestString();
        $module = $this->_request->getModuleName();

        $editUri = 'customer/account/edit';

        switch (true) {

            case (stripos($requestString, 'customer/account/logout') !== false
                || stripos($requestString, 'customer/section/load') !== false):
                break;

            case $moduleName = (stripos($module, 'customer') !== false) ? 'customer' : null:
                if ($this->customerSession->isLoggedIn() &&
                    $this->fakeEmail->detect($this->customerSession->getCustomer()->getEmail())
                ) {
                    $this->messageManager->getMessages()->deleteMessageByIdentifier('fakeemail');
                    $message = $this->getChangeEmailWithLinkMessage($editUri);

                    if ('customer' === $moduleName) {
                        if (stripos($requestString, $editUri) !== false) {
                            $message = $this->getChangeEmailWithoutLinkMessage();
                        }
                        $noticeMessage = $this->noticeFactory->create(['text' => $message])->setIdentifier('fakeemail');
                        $this->messageManager->addUniqueMessages([$noticeMessage]);
                    }

                }
                break;
        }
    }

    /**
     * @param string $editUri
     * @return \Magento\Framework\Phrase
     */
    protected function getChangeEmailWithLinkMessage(string $editUri): Phrase
    {
        return __(
            'Your account needs to be updated. The email address in your profile is invalid. ' .
            'Please indicate your valid email address by going to the <a href="%1">Account edit page</a>',
            $this->store->getUrl($editUri)
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getChangeEmailWithoutLinkMessage(): Phrase
    {
        return __('Your account needs to be updated. The email address in your profile is invalid. ' .
                  'Please indicate your valid email address.');
    }
}
