<?php
namespace Dynamic\OrderStatus\Controller;
use Magento\Framework\App\Action\Context;
class Token 
{
    /**
    * @var \Magento\Customer\Model\Session
    */
    protected $_customerSession;
    /**
    * @param Context          $context
    * @param Session          $customerSession
    * @SuppressWarnings(PHPMD.ExcessiveParameterList)
    */
    public function __construct(
        Context $context,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenModelFactory
    ) {
        
        $this->_tokenModelFactory = $tokenModelFactory;
    }
    public function toOptionArray()
    {

        $Token = $this->_tokenModelFactory->create()->getCollection();
        $option = [];
        foreach($Token as $tokan){
            if($tokan['customer_id'] == ''){
            $option[] = array('label' => $tokan['token'] , 'value' => $tokan['token']);
            }
        }
        return $option;    
    }
}