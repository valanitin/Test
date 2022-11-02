<?php
namespace Dynamic\Mytickets\Block\Adminhtml\Mytickets;

/**
 * Adminhtml Mytickets grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $_mytickets;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Mytickets\Model\Mytickets $myticketsPage
     * @param \Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        \Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_mytickets = $mytickets;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('myticketsGrid');
        $this->setDefaultSort('mytickets_id');
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
        /* @var $collection \Dynamic\Mytickets\Model\ResourceModel\Mytickets\Collection */
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
        $this->addColumn('mytickets_id', [
            'header'    => __('ID'),
            'index'     => 'mytickets_id',
        ]);
       
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('last_name', ['header' => __('Last Name'), 'index' => 'last_name']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn('phone', ['header' => __('Phone'), 'index' => 'phone']);
        $this->addColumn('brand', ['header' => __('Brand'), 'index' => 'brand']);
        $this->addColumn('style', ['header' => __('Style'), 'index' => 'style']);
        $this->addColumn('keyword', ['header' => __('Keyword'), 'index' => 'keyword']);
        $this->addColumn('image', ['header' => __('Image'), 'index' => 'image']);
        $this->addColumn('ticket_code', ['header' => __('Ticket Code'), 'index' => 'ticket_code']);
        $this->addColumn('remarks', ['header' => __('Remarks'), 'index' => 'remarks']);

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
