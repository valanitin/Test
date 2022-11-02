<?php
namespace Dynamic\Mytickets\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Myticketslist extends Template
{
    /**
     * @var \Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory
     */
    protected $_myticketsollectionFactory;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;
    
    public function __construct(
        Context $context,
        \Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory $myticketsollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_myticketsollectionFactory = $myticketsollectionFactory;
        $this->customerSession = $customerSession;
        $this->timezone = $timezone;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->gettMyTicketsCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'item.history.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 20 => 20])->setShowPerPage(true)->setCollection($this->gettMyTicketsCollection());
            $this->setChild('pager', $pager);
            $this->gettMyTicketsCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return \Dynamic\Mytickets\Model\ResourceModel\Mytickets\Collection|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function gettMyTicketsCollection()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        $customerEmail = $this->customerSession->getCustomer()->getEmail();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_myticketsollectionFactory->create()->addFieldToFilter('email', ['eq' => $customerEmail]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $collection->setOrder('created_at', 'DESC');
        return $collection;
    }

    /**
     * @return \Dynamic\Mytickets\Model\ResourceModel\Mytickets\Collection|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getMyTicketsByCustomerId()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $customerId = $this->customerSession->getCustomer()->getId();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_myticketsollectionFactory->create()
                        ->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * @param string $sku
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkMyTicketsRequest($sku)
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $customerEmail = $this->customerSession->getCustomer()->getEmail();
        $collection = $this->_myticketsollectionFactory->create()
                    ->addFieldToFilter('email', ['eq' => $customerEmail])
                    ->addFieldToFilter('style', ['eq' => $sku])
                    ->addFieldToFilter('keyword', ['like' => '%Return%']);

        return count($collection);
    }

    /**
     * @param string $sku
     * @return int|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkMyTicketsCancel($sku)
    {
        $collection = $this->_myticketsollectionFactory->create()
                    ->addFieldToFilter('style', ['eq' => $sku])
                    ->addFieldToFilter('keyword', ['like' => 'Cancel%']);
        return count($collection);
    }
    
    /**
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isLoggedIn()
    {
        if (empty($this->customerSession)) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }

        return false;
    }
  
    /** @phpstan-ignore-line */
    /* @phpstan-ignore-next-line */
    public function formarDate($date)
    {
        /* @phpstan-ignore-next-line */
        $this->timezone
            ->date(new \DateTime($date))
            ->format('Y/m/d H:i:s');
      /*$this->timezone->formatDate(
                        $date,
                        \IntlDateFormatter::FULL,
                        false
               );*/
    }
}
