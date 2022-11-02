<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Controller\Adminhtml\GiftUser;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
   /**
    * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $_resultPageFactory;

   /**
    * @param \Magento\Backend\App\Action\Context        $context
    * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Card'));
        return $resultPage;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Firas_GiftCard::giftuser_index');
    }
}
