<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Plugin\Magento;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\RedirectFactory;
use Plumrocket\SocialLoginFree\Controller\Account\Login;
use Plumrocket\SocialLoginFree\Controller\Account\LoginPost;

/**
 * @since 3.0.2
 */
class CsrfValidatorPlugin
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * CsrfValidatorPlugin constructor.
     *
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        RedirectFactory $redirectFactory
    ) {
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param                                        $csrfValidator
     * @param callable                               $proceed
     * @param \Magento\Framework\App\Request\Http    $request
     * @param \Magento\Framework\App\ActionInterface $action
     * @throws \Magento\Framework\App\Request\InvalidRequestException
     */
    public function aroundValidate($csrfValidator, callable $proceed, HttpRequest $request, ActionInterface $action)
    {
        if ($action instanceof LoginPost) {
            $valid = $action->validateForCsrf($request);
            if (!$valid) {
                throw $this->createException($request, $action);
            }
        } else {
            $proceed($request, $action);
        }
    }

    /**
     * Create exception for when incoming request failed validation.
     *
     * @param HttpRequest $request
     * @param ActionInterface $action
     *
     * @return InvalidRequestException
     */
    private function createException(
        HttpRequest $request,
        ActionInterface $action
    ) {
        $exception = null;

        if ($action instanceof Login) {
            $exception = $action->createCsrfValidationException($request);
        }
        if (!$exception) {
            $response = $this->redirectFactory->create()
                ->setRefererOrBaseUrl()
                ->setHttpResponseCode(302);
            $messages = [
                __('Invalid Form Key. Please refresh the page.'),
            ];
            $exception = new InvalidRequestException($response, $messages);
        }

        return $exception;
    }
}
