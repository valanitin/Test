<?php
namespace Dynamic\Cmspagemanager\Block\Adminhtml\Cmspagemanager\Edit\Tab;

/**
 * Cmspagemanager edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_rendererFieldset;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_rendererFieldset = $rendererFieldset;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('cmspagemanager');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Dynamic_Cmspagemanager::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('cmspagemanager_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('CMS Manager')]);

        if ($model->getId()) {
            $fieldset->addField('cmspagemanager_id', 'hidden', ['name' => 'cmspagemanager_id']);
        }

        $cmsCollectionArr = \Magento\Framework\App\ObjectManager::getInstance()->create('Dynamic\Cmspagemanager\Model\CmsConfig');

        $fieldset->addField(
            'cms_id',
            'select',
            array(
                'name' => 'cms_id',
                'label' => __('Select CMS Page'),
                'title' => __('Select CMS Page'),
                'values' => $cmsCollectionArr->getCmsArray(),
                'required' => true,
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => ['1' => 'Enable', '0' => 'Disabled'],
                'required' => true
            )
        );

        $fieldset->addField(
            'extra_data',
            'textarea',
            array(
                'name' => 'extra_data',
                'label' => __('Extra Data'),
                'title' => __('Extra Data'),
                'required'  => false,
            )
        );

        $fieldset->addField('cms_text', 'hidden', ['name' => 'cms_text'])->setRenderer($this->_rendererFieldset->setTemplate('Dynamic_Cmspagemanager::cms_text.phtml'))->setCmsText($model->getCmsText());
        
        $this->_eventManager->dispatch('adminhtml_cmspagemanager_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('CMS Manager');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('CMS Manager');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}


        