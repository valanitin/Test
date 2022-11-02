<?php
/**
 * Grid Record Index Controller.
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2017 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */
namespace Firas\Grid\Controller\Adminhtml\Grid;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Mapped eBay Order List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Firas_Grid::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Price List'));
        return $resultPage;
    }

    /**
     * Check Order Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Firas_Grid::grid_list');
    }
}
