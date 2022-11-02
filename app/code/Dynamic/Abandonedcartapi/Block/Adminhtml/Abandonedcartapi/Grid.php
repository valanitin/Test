<?php
namespace Dynamic\Abandonedcartapi\Block\Adminhtml\Abandonedcartapi;

/**
 * Adminhtml Abandonedcartapi grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Abandonedcartapi\Model\Abandonedcartapi
     */
    protected $_abandonedcartapi;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Abandonedcartapi\Model\Abandonedcartapi $abandonedcartapiPage
     * @param \Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Abandonedcartapi\Model\Abandonedcartapi $abandonedcartapi,
        \Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_abandonedcartapi = $abandonedcartapi;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('abandonedcartapiGrid');
        $this->setDefaultSort('abandonedcartapi_id');
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
        /* @var $collection \Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi\Collection */
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
        $this->addColumn('abandonedcartapi_id', [
            'header'    => __('ID'),
            'index'     => 'abandonedcartapi_id',
        ]);
       
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('lang_code', ['header' => __('Lang Code'), 'index' => 'lang_code']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn('item_info', ['header' => __('Item Info Json'), 'index' => 'item_info']);

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
