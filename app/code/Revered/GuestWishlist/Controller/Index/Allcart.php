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

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Action\Context;
use Revered\GuestWishlist\Model\ItemCarrier;
use Magento\Framework\Controller\ResultFactory;

class Allcart extends AbstractIndex
{
    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var ItemCarrier
     */
    protected $itemCarrier;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * Allcart constructor.
     * @param Context $context
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param Validator $formKeyValidator
     * @param ItemCarrier $itemCarrier
     */
    public function __construct(
        Context $context,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider,
        Validator $formKeyValidator,
        ItemCarrier $itemCarrier
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->formKeyValidator = $formKeyValidator;
        $this->itemCarrier = $itemCarrier;
        parent::__construct($context);
    }

    /**
     * Add all items from wishlist to shopping cart
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            $resultForward->forward('noroute');
            return $resultForward;
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->itemCarrier->moveAllToCart($wishlist, $this->getRequest()->getParam('qty'));
        $resultRedirect->setUrl($redirectUrl);

        if ($this->getRequest()->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);
            return $resultJson;
        }

        return $resultRedirect;
    }
}
