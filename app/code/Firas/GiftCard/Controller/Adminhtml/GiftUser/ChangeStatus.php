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
use Magento\Ui\Component\MassAction\Filter;

class ChangeStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Firas\GiftCard\Model\ResourceModel\GiftUser\CollectionFactory
     */
    protected $_giftUserCollection;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @param \Magento\Backend\App\Action\Context                              $context
     * @param \Firas\GiftCard\Model\ResourceModel\GiftUser\CollectionFactory $giftUserCollection
     * @param Filter                                                           $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Firas\GiftCard\Model\ResourceModel\GiftUser\CollectionFactory $giftUserCollection,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->_giftUserCollection = $giftUserCollection;
        $this->_filter = $filter;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $GiftCardIds = $this->_filter->getCollection($this->_giftUserCollection->create());
        try {
            foreach ($GiftCardIds as $GiftCardId) {
                $GiftCardId->setIsActive($this->getRequest()->getParam('entity_id'))->save();
            }
            $this->messageManager->addSuccess(__('Total of %1 record(s) were successfully updated', $GiftCardIds->getSize()));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect->setPath('giftcard/giftuser/index');
        return $resultRedirect;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Firas_GiftCard::giftuser_index');
    }
}
