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

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractIndex
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;
    /**
     * @var
     */
    protected $_session;
    /**
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Action\Context $context,
        CustomerSession $session,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->_session = $session;
        parent::__construct($context);
    }

    /**
     * Display customer wishlist
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if($this->_session->isLoggedIn()){
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('wishlist/');
        }
        if (!$this->wishlistProvider->getWishlist()) {
            throw new NotFoundException(__('Page not found.'));
        }
        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
