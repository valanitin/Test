<?php

namespace Ludxb\Continueshopping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    
    protected $_checkoutSession;
    protected $request;

    public function __construct(
        Context                                    $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request
    )
    {
       $this->_checkoutSession = $checkoutSession;
       $this->request = $request;
    }

    public function getLastUrl(){
        return $this->request->getServer('HTTP_REFERER');
    }

    public function getSessionLastUrl(){
        return $this->_checkoutSession->getLastVisitedUrl();
    }
   
}

