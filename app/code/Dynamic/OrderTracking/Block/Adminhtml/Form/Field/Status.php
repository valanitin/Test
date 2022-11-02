<?php

namespace Dynamic\OrderTracking\Block\Adminhtml\Form\Field;

use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use \Magento\Framework\DataObject;

class Status extends AbstractFieldArray
{
    protected $optionField;

    protected function _prepareToRender()
    {
        $this->addColumn('status_title', ['label' => __('Status Title'), 'class' => 'required-entry']);
        $this->addColumn('status_code', ['label' => __('Status Code'), 'class' => 'required-entry']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }

}