<?php

namespace Dynamic\Orderreturn\Block\Account;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Dynamic\Orderreturn\Model\Orderreturn;
use Magento\Customer\Model\Session;

class ReturnList extends Template
{
	protected $orderReturnCollection;

	public $customerSession;

	public function __construct(Context $context, Orderreturn $orderReturnCollection, Session $customerSession)
	{
		$this->orderReturnCollection = $orderReturnCollection;
		$this->customerSession = $customerSession;
		parent::__construct($context);
	}

	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if ($this->getItemCollection()) {
			$pager = $this->getLayout()->createBlock(
				'Magento\Theme\Block\Html\Pager',
				'item.history.pager'
			)->setAvailableLimit([10 => 10, 20 => 20, 50 => 50])->setShowPerPage(true)->setCollection($this->getItemCollection());
			$this->setChild('pager', $pager);
			$this->getItemCollection()->load();
		}
		return $this;
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getItemCollection()
	{
		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
		$collection = $this->orderReturnCollection->getCollection();
		$collection->setOrder('orderreturn_id ','DESC');
		$collection->addFieldToFilter('customer_email', ['eq' => $this->customerSession->getCustomer()->getEmail()]);
		$collection->setPageSize($pageSize);
		$collection->setCurPage($page);
		return $collection;
	}
}
