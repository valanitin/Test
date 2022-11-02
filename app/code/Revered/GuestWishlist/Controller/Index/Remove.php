<?php
/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */
namespace Revered\GuestWishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Remove
 * @package Revered\GuestWishlist\Controller\Index
 */
class Remove extends AbstractIndex
{
    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Remove constructor.
     * @param Action\Context $context
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider,
        Validator $formKeyValidator
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Remove item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('item');
        $item = $this->_objectManager->create(\Revered\GuestWishlist\Model\Item::class)->load($id);
        if (!$item->getId()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(
                __('We can\'t delete the item from Wish List right now because of an error: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t delete the item from the Wish List right now.'));
        }

        $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
        $request = $this->getRequest();
        $refererUrl = (string)$request->getServer('HTTP_REFERER');
        $url = (string)$request->getParam(\Magento\Framework\App\Response\RedirectInterface::PARAM_NAME_REFERER_URL);
        if ($url) {
            $refererUrl = $url;
        }
        if ($request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED) && $refererUrl) {
            $redirectUrl = $refererUrl;
        } else {
            $redirectUrl = $this->_redirect->getRedirectUrl($this->_url->getUrl('*/*'));
        }
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
