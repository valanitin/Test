<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\Amp\Model\Plugin\MagentoTwoTwo;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;

class CsrfValidatorPlugin
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var string
     */
    private $oldAmpEmailInterface = '\Plumrocket\AmpEmail\Model\MagentoTwoTwo\CsrfAwareActionInterface';
    private $ampEmailInterface = '\Plumrocket\AmpEmailApi\Model\MagentoTwoTwo\CsrfAwareActionInterface';

    /**
     * CsrfValidatorPlugin constructor.
     *
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
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
        if ($action instanceof \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
            || $action instanceof $this->oldAmpEmailInterface
            || $action instanceof $this->ampEmailInterface
        ) {
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
    ) : InvalidRequestException {
        $exception = null;

        if ($action instanceof \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
            || $action instanceof $this->oldAmpEmailInterface
            || $action instanceof $this->ampEmailInterface
        ) {
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
