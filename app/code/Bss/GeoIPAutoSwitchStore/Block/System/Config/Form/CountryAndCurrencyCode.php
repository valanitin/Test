<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Block\System\Config\Form;

use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class CountryAndCurrencyCode extends AbstractFieldArray
{

    /**
     *
     */
    protected function _prepareToRender()
    {
        $this->addColumn('country', ['label' => __('Country'), 'style' => 'width:200px']);
        $this->addColumn('country_code', ['label' => __('Country Code'), 'style' => 'width:90px', 'class' => 'required-entry']);
        $this->addColumn('currency_code', ['label' => __('Currency Code'), 'style' => 'width:90px', 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Row');
    }
}
