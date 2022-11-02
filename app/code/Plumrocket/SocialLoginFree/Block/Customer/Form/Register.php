<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Customer\Form;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;

class Register extends Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * Register constructor.
     *
     * @param \Magento\Customer\Model\Session                          $customerSession
     * @param \Magento\Framework\View\Element\Template\Context         $context
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail $fakeEmail
     * @param array                                                    $data
     */
    public function __construct(
        Session $customerSession,
        Context $context,
        FakeEmail $fakeEmail,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->fakeEmail = $fakeEmail;
    }

    /**
     * @return array|null
     */
    public function getPsloginData()
    {
        $data = $this->customerSession->getPsloginFields();
        $this->customerSession->unsPsloginFields();

        return $data;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isFakeEmail($email): bool
    {
        if ($email === null) {
            return true;
        }

        return $this->fakeEmail->detect($email);
    }
}
