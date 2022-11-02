<?php
namespace Ludxb\Continueshopping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class Addtocart implements ObserverInterface
{
    protected $request;
    protected $_checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
         \Magento\Framework\App\RequestInterface $request
    ) {
    
        $this->_checkoutSession = $checkoutSession;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $lastUrl = $param = $this->request->getParam('lastvisitedurl');
        $this->_checkoutSession->setLastVisitedUrl($lastUrl);
        $lastUrl = $this->_checkoutSession->getLastVisitedUrl();
    }
}

