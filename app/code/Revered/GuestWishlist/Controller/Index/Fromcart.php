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

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Class Fromcart
 * @package Revered\GuestWishlist\Controller\Index
 */
class Fromcart extends AbstractIndex
{
    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface|WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Fromcart constructor.
     * @param Action\Context $context
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param WishlistHelper $wishlistHelper
     * @param CheckoutCart $cart
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider,
        WishlistHelper $wishlistHelper,
        CheckoutCart $cart,
        CartHelper $cartHelper,
        Escaper $escaper,
        Validator $formKeyValidator
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->wishlistHelper = $wishlistHelper;
        $this->cart = $cart;
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Add cart item to wishlist and remove from cart
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        try {
            $itemId = (int)$this->getRequest()->getParam('item');
            $item = $this->cart->getQuote()->getItemById($itemId);
            if (!$item) {
                throw new LocalizedException(
                    __('The requested cart item doesn\'t exist.')
                );
            }

            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            $wishlist->addNewItem($productId, $buyRequest);

            $this->cart->getQuote()->removeItem($itemId);
            $this->cart->save();

            $this->wishlistHelper->calculate();
            $wishlist->save();

            $this->messageManager->addSuccessMessage(__(
                "%1 has been moved to your wish list.",
                $this->escaper->escapeHtml($item->getProduct()->getName())
            ));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t move the item to the wish list.'));
        }
        return $resultRedirect->setUrl($this->cartHelper->getCartUrl());
    }
}
