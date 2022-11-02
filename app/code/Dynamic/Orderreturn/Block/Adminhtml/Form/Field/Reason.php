<?php

namespace Dynamic\Orderreturn\Block\Adminhtml\Form\Field;

use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use \Magento\Framework\DataObject;

class Reason extends AbstractFieldArray
{
    protected $optionField;

    protected function _prepareToRender()
    {
        $this->addColumn('reason_data', ['label' => __('Reason'), 'class' => 'required-entry']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }

}