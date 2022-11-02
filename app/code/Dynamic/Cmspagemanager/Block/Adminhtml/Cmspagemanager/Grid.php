<?php
namespace Dynamic\Cmspagemanager\Block\Adminhtml\Cmspagemanager;

/**
 * Adminhtml Cmspagemanager grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Dynamic\Cmspagemanager\Model\Cmspagemanager
     */
    protected $_cmspagemanager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dynamic\Cmspagemanager\Model\Cmspagemanager $cmspagemanagerPage
     * @param \Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dynamic\Cmspagemanager\Model\Cmspagemanager $cmspagemanager,
        \Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_cmspagemanager = $cmspagemanager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cmspagemanagerGrid');
        $this->setDefaultSort('cmspagemanager_id');
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
        /* @var $collection \Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager\Collection */
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
        $this->addColumn('cmspagemanager_id', [
            'header'    => __('ID'),
            'index'     => 'cmspagemanager_id',
        ]);

        $cmsCollectionArr = \Magento\Framework\App\ObjectManager::getInstance()->create('Dynamic\Cmspagemanager\Model\CmsConfig');
        $this->addColumn(
            'cms_id',
            [
                'header' => __('CMS Page'),
                'index' => 'cms_id',
                'class' => 'cms_id',
                'type' => 'options',
                'options' => $cmsCollectionArr->getCmsArray(),                
            ]
        );

        $arrItemStatus = array('1' => __('Enable'), '0' => __('Disable'));
        $this->addColumn('status', ['header' => __('Status'), 'type' => 'options', 'options' => $arrItemStatus, 'index' => 'status']);

        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'cmspagemanager_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

   protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cmspagemanager_id');
        $this->getMassactionBlock()->setFormFieldName('cmspagemanager_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('cmspagemanager/*/massDelete'),
                'confirm' => __('Are you sure want to delete selected item(s)?')
            )
        );

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('cmspagemanager/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => [['label' => '', 'value' => ''], ['label' => 'Enable', 'value' => 1], ['label' => 'Disable', 'value' => 0]]
                    ]
                ]
            ]
        );
        
        return $this;
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['cmspagemanager_id' => $row->getId()]);
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
