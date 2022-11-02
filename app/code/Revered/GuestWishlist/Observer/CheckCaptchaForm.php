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
namespace Revered\GuestWishlist\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
/**
 * Class CustomerAuthenticated
 * @package Revered\GuestWishlist\Observer
 */
class CheckCaptchaForm implements ObserverInterface
{
    const FORM_ID='guest_wishlist_form';

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlManager;
    /**
     * @var \Magento\Captcha\Observer\CaptchaStringResolver
     */
    protected $_captchaStringResolver;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    public function __construct(\Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->_messageManager = $messageManager;
        $this->_session = $session;
        $this->_urlManager = $urlManager;
        $this->_redirect = $redirect;
        $this->_captchaStringResolver = $captchaStringResolver;
    }

    public function execute(Observer $observer) {
        $captchaModel = $this->_helper->getCaptcha(self::FORM_ID);
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();
        if ($captchaModel->isRequired()) {
            if (!$captchaModel->isCorrect($this->_captchaStringResolver->resolve($request, self::FORM_ID))) {
                $this->_messageManager->addError(__('Incorrect CAPTCHA'));
                $this->_actionFlag->set('', \Magento\Framework\App\Action::FLAG_NO_DISPATCH, true);
                $url = $this->_urlManager->getUrl('*/*/share/');
                $controller->getResponse()->setRedirect($this->_redirect->error($url));
            }
        }
        return $this;
    }
}