<?php
namespace Dynamic\Referfriend\Block\Adminhtml\Referfriend;

/**
 * Adminhtml Referfriend grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Referfriend\Model\ResourceModel\Referfriend\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Referfriend\Model\Referfriend
     */
    protected $_referfriend;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Referfriend\Model\Referfriend $referfriendPage
     * @param \Dynamic\Referfriend\Model\ResourceModel\Referfriend\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Referfriend\Model\Referfriend $referfriend,
        \Dynamic\Referfriend\Model\ResourceModel\Referfriend\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_referfriend = $referfriend;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('referfriendGrid');
        $this->setDefaultSort('referfriend_id');
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
        /* @var $collection \Dynamic\Referfriend\Model\ResourceModel\Referfriend\Collection */
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
        $this->addColumn('referfriend_id', [
            'header'    => __('ID'),
            'index'     => 'referfriend_id',
        ]);
       
        $this->addColumn('referrer_first_name', ['header' => __('Your First Name'), 'index' => 'referrer_first_name']);
        $this->addColumn('referrer_email', ['header' => __('Email Address'), 'index' => 'referrer_email']);
        $this->addColumn('referrer_phone', ['header' => __('Your Phone Number'), 'index' => 'referrer_phone']);
        $this->addColumn('yourfirstname', ['header' => __('Referring Friend First Name'), 'index' => 'yourfirstname']);
        $this->addColumn('youremailaddress', ['header' => __('Referring Friend Email Address'), 'index' => 'youremailaddress']);
        $this->addColumn('yourphonenumber', ['header' => __('Referring Friend Phone Number'), 'index' => 'yourphonenumber']);
        $this->addColumn('referrer_code', ['header' => __('Referrer Code'), 'index' => 'referrer_code']);

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
