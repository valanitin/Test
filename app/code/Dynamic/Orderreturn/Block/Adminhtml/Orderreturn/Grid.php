<?php
namespace Dynamic\Orderreturn\Block\Adminhtml\Orderreturn;

/**
 * Adminhtml Orderreturn grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Orderreturn\Model\ResourceModel\Orderreturn\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Orderreturn\Model\Orderreturn
     */
    protected $_orderreturn;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Orderreturn\Model\Orderreturn $orderreturnPage
     * @param \Dynamic\Orderreturn\Model\ResourceModel\Orderreturn\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn,
        \Dynamic\Orderreturn\Model\ResourceModel\Orderreturn\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderreturn = $orderreturn;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderreturnGrid');
        $this->setDefaultSort('orderreturn_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Dynamic\Orderreturn\Model\ResourceModel\Orderreturn\Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('orderreturn_id', [
            'header'    => __('ID'),
            'index'     => 'orderreturn_id',
        ]);
        $arrFlagStatus = array('1' => __('Completed'), '0' => __('Pending'), '2' => __('Failed'));

        $this->addColumn('order_id', ['header' => __('Order Id'), 'index' => 'order_id']);
        $this->addColumn('product_sku', ['header' => __('Product Sku'), 'index' => 'product_sku']);
        $this->addColumn('customer_email', ['header' => __('Customer Email'), 'index' => 'customer_email']);
        $this->addColumn('reason', ['header' => __('Return Reason'), 'index' => 'reason']);
        $this->addColumn('status', ['header' => __('Status'), 'type' => 'options', 'options' => $arrFlagStatus, 'index' => 'status']);
        
        $orderReturnStatusCollectionArr = \Magento\Framework\App\ObjectManager::getInstance()->create('Dynamic\Orderreturn\Model\ReturnConfig');

        $this->addColumn(
            'erp_return_status',
            [
                'header' => __('ERP Return Status'),
                'index' => 'erp_return_status',
                'class' => 'erp_return_status',
                'type' => 'options',
                'options' => $orderReturnStatusCollectionArr->getReturnStatusArray(),                
            ]
        );
        $this->addColumn('created_at', ['header' => __('Applied For Return Date'), 'index' => 'created_at']);

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
