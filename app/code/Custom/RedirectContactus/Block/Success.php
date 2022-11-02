<?php

namespace Custom\RedirectContactus\Block;
 
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $session,
        array $data = []
    )
    {
        $this->_customerSession = $session;
        parent::__construct($context, $data);
    }


    public function customIsLoggedIn() {
        if ($this->_customerSession->isLoggedIn() == true){

            return true;
        } else return false;

    }
}