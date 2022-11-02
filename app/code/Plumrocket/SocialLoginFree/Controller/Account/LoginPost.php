<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\Account;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;

class LoginPost extends Action
{
    public function execute()
    {
        if ($redirectUrl = $this->getRequest()->getParam(Url::REFERER_QUERY_PARAM_NAME)) {
            $redirectUrl = base64_decode($redirectUrl); //phpcs:ignore -- encode url to avoid conflicts in url
            $this->getResponse()->setRedirect($redirectUrl);
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function validateForCsrf(RequestInterface $request)
    {
        return true;
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return |null
     */
    public function createCsrfValidationException(RequestInterface $request)
    {
        return null;
    }
}
