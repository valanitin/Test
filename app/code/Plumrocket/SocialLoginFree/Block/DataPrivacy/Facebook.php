<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Block\DataPrivacy;

use Magento\Framework\View\Element\Template;
use Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager;
use Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest;

/**
 * @since 3.1.0
 */
class Facebook extends Template
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager
     */
    private $deletionRequestManager;

    /**
     * phpcs:disable Generic.Files.LineLength
     * @param \Magento\Framework\View\Element\Template\Context                                       $context
     * @param \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager $deletionRequestManager
     * @param array                                                                                  $data
     * phpcs:enable Generic.Files.LineLength
     */
    public function __construct(
        Template\Context $context,
        AutomaticDeletionRequestManager $deletionRequestManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->deletionRequestManager = $deletionRequestManager;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getStatusTitle(): \Magento\Framework\Phrase
    {
        $confirmationCode = $this->getRequest()->getParam('confirmationCode');

        switch ($this->deletionRequestManager->getStatus($confirmationCode)) {
            case DeletionRequest::COMPLETE:
                return __('Your data deletion request is complete (request ID: %1)', $confirmationCode);
            default:
                return __('Deletion request not found.');
        }
    }
}
