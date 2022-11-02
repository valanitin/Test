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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Api\Wishlist;

use \Magento\Framework\Message\MessageInterface;
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Wishlist\Controller\Index\Add implements
    \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    use \Plumrocket\Amp\Controller\ValidateForCsrfTrait;

    /**
     * @var \Magento\Framework\Escaper|null
     */
    private $escaper;

    /**
     * @var \Plumrocket\Amp\Model\Result\AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context                  $context
     * @param \Magento\Customer\Model\Session                        $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Catalog\Api\ProductRepositoryInterface        $productRepository
     * @param \Magento\Framework\Data\Form\FormKey\Validator         $formKeyValidator
     * @param \Plumrocket\Amp\Model\Validator                        $validator
     * @param \Magento\Framework\Escaper                             $escaper
     * @param \Plumrocket\Amp\Model\Result\AmpJsonFactory            $ampResultFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Plumrocket\Amp\Model\Validator $validator,
        \Magento\Framework\Escaper $escaper,
        \Plumrocket\Amp\Model\Result\AmpJsonFactory $ampResultFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $wishlistProvider,
            $productRepository,
            $formKeyValidator
        );
        $this->escaper = $escaper;
        $this->formKeyValidator = $validator;
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * Refactor response to json
     * Ignore form key validation
     *
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $errorCount = $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_ERROR);
        $successCount = $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_SUCCESS);

        // Magento will redirect on the same action after login
        // For forwards, example: customer/account/loginPost/
        if (! $this->_request->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            parent::execute();

            if ($successCount < $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_SUCCESS)) {
                $wishlist = $this->wishlistProvider->getWishlist();
                return $resultRedirect->setPath('wishlist', ['wishlist_id' => $wishlist->getId()]);
            }

            return $resultRedirect->setPath('wishlist/');
        }

        // For amp xhr request
        $this->_request->setParam('ajax', null);
        $result = $this->ampResultFactory->create();

        // If customer not logged in - redirect to login
        if (! $this->_customerSession->isLoggedIn()) {
            $this->prepareReferer();
            $result->addErrorMessage(__('You will be redirected to the login page'));
            $result->setFormRedirect($this->_url->getUrl(\Magento\Customer\Model\Url::ROUTE_ACCOUNT_LOGIN));
            return $result;
        }

        // Magento add to wishlist
        parent::execute();

        // Handle Error messages
        if ($errorCount < $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_ERROR)) {
            $items = $this->messageManager->getMessages(true)->getItemsByType(MessageInterface::TYPE_ERROR);

            foreach ($items as $message) {
                $result->addErrorMessage($message->getText());
            }

            return $result;
        }

        // Handle Success messages
        if ($successCount < $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_SUCCESS)) {
            $items = $this->messageManager->getMessages(false)->getItemsByType(MessageInterface::TYPE_SUCCESS);
            $messageText = null;
            foreach ($items as $message) {
                if (null === $message && isset($message->getData()['product_name'])) {
                    $messageText = $this->escaper->escapeHtml(__(
                        '%1 has been added to your Wish List.',
                        $this->escaper->escapeHtml($message->getData()['product_name']) ?: null
                    ));
                }
            }
            $result->addSuccessMessage($messageText);

            $wishlist = $this->wishlistProvider->getWishlist();

            $result->setFormRedirect(
                $this->_url->getUrl('wishlist/index/index', ['wishlist_id' => $wishlist->getId()])
            );
        }

        return $result;
    }

    /**
     * Set referer and before data
     *
     * @return $this
     */
    private function prepareReferer()
    {
        if (!$this->_customerSession->getBeforeWishlistUrl()) {
            $this->_customerSession->setBeforeWishlistUrl($this->_redirect->getRefererUrl());
        }
        $this->_customerSession->setBeforeWishlistRequest($this->_request->getParams());
        $this->_customerSession->setBeforeRequestParams($this->_customerSession->getBeforeWishlistRequest());
        $this->_customerSession->setBeforeModuleName('pramp');
        $this->_customerSession->setBeforeControllerName('api_wishlist');
        $this->_customerSession->setBeforeAction('add');

        return $this;
    }
}
