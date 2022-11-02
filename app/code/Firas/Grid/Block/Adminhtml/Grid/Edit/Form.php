<?php
/**
 * Firas_Grid Add New Row Form Admin Block.
 * @category    Firas
 * @package     Firas_Grid
 * @author      Firas Software Private Limited
 *
 */
namespace Firas\Grid\Block\Adminhtml\Grid\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \Firas\Grid\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Firas\Grid\Model\Status $options,
        array $data = []
    ) {
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('wkgrid_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'shipping_country_code',
            'text',
            [
                'name' => 'shipping_country_code',
                'label' => __('Shipping Country Code'),
                'id' => 'shipping_country_code',
                'title' => __('Shipping Country Code'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'shipping_country_name',
            'text',
            [
                'name' => 'shipping_country_name',
                'label' => __('Shipping Country Name'),
                'id' => 'shipping_country_name',
                'title' => __('Shipping Country Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'shipping_price',
            'text',
            [
                'name' => 'shipping_price',
                'label' => __('Shipping Price'),
                'id' => 'shipping_price',
                'title' => __('Shipping Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'shipping_price_currency',
            'text',
            [
                'name' => 'shipping_price_currency',
                'label' => __('Shipping Price Currency'),
                'id' => 'shipping_price_currency',
                'title' => __('Shipping Price Currency'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
