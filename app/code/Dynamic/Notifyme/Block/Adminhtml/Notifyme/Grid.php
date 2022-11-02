<?php
namespace Dynamic\Notifyme\Block\Adminhtml\Notifyme;

/**
 * Adminhtml Notifyme grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Notifyme\Model\ResourceModel\Notifyme\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Notifyme\Model\Notifyme
     */
    protected $_notifyme;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Notifyme\Model\Notifyme $notifymePage
     * @param \Dynamic\Notifyme\Model\ResourceModel\Notifyme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Notifyme\Model\Notifyme $notifyme,
        \Dynamic\Notifyme\Model\ResourceModel\Notifyme\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_notifyme = $notifyme;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('notifymeGrid');
        $this->setDefaultSort('notifyme_id');
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
        /* @var $collection \Dynamic\Notifyme\Model\ResourceModel\Notifyme\Collection */
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
        $this->addColumn('notifyme_id', [
            'header'    => __('ID'),
            'index'     => 'notifyme_id',
        ]);
       
        $this->addColumn('email', ['header' => __('Email Address'), 'index' => 'email']);
        $this->addColumn('product_sku', ['header' => __('Product SKU'), 'index' => 'product_sku']);
        $this->addColumn('product_size', ['header' => __('Product Size'), 'index' => 'product_size']);

        $arrFlagStatus = array('1' => __('Completed'), '0' => __('Pending'));
        $this->addColumn('status', ['header' => __('Status'), 'type' => 'options', 'options' => $arrFlagStatus, 'index' => 'status']);

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
