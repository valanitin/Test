<?php
namespace Zealousweb\AppleLogin\Controller\Apple;

use Zealousweb\AppleLogin\Helper\Data as AppleHelper;

class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultFactory;

    /**
     * @var \Zealousweb\AppleLogin\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Zealousweb\AppleLogin\Helper\Data $helper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\Controller\Result\RawFactory $rowFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Zealousweb\AppleLogin\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->helper = $helper;
    }
    
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $url = $this->helper->getAuthorizationUrl(); 
        $redirect->setUrl($url);
        return $redirect; 
    }
}