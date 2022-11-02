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
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Configure
 * @package Revered\GuestWishlist\Controller\Index
 */
class Configure extends AbstractIndex
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Configure constructor.
     * @param Action\Context $context
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Action to reconfigure wishlist item
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            /* @var $item \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create(\Revered\GuestWishlist\Model\Item::class);
            $item->loadWithOptions($id);
            if (!$item->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We can\'t load the Wish List item right now.')
                );
            }
            $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            $this->_coreRegistry->register('wishlist_item', $item);

            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $buyRequest = $item->getBuyRequest();
            if (!$buyRequest->getQty() && $item->getQty()) {
                $buyRequest->setQty($item->getQty());
            }
            if ($buyRequest->getQty() && !$item->getQty()) {
                $item->setQty($buyRequest->getQty());
                $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
            }
            $params->setBuyRequest($buyRequest);
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->_objectManager->get(
                \Magento\Catalog\Helper\Product\View::class
            )->prepareAndRender(
                $resultPage,
                $item->getProductId(),
                $this,
                $params
            );

            return $resultPage;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect->setPath('*');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t configure the product right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $resultRedirect->setPath('*');
            return $resultRedirect;
        }
    }
}
