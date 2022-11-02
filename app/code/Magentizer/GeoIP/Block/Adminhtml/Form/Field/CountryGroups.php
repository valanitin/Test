<?php
namespace Magentizer\GeoIP\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magentizer\GeoIP\Block\Adminhtml\Form\Field\StoreColumn;

/**
 * Class Ranges
 */
class CountryGroups extends AbstractFieldArray
{
    /**
     * @var StoreColumn
     */
    private $storeRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('group', [
            'label' => __('Group'),
            'renderer' => $this->getStoreRenderer()
        ]);
        $this->addColumn('country_ids', ['label' => __('Country Ids'), 'class' => 'required-entry']);
        $this->addColumn('default_country_pricing', ['label' => __('Group Country ID for Pricing'), 'class' => 'required-entry']);        
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $tax = $row->getTax();
        if ($tax !== null) {
            $options['option_' . $this->getStoreRenderer()->calcOptionHash($tax)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return StoreColumn
     * @throws LocalizedException
     */
    private function getStoreRenderer()
    {
        if (!$this->storeRenderer) {
            $this->storeRenderer = $this->getLayout()->createBlock(
                StoreColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->storeRenderer;
    }
}