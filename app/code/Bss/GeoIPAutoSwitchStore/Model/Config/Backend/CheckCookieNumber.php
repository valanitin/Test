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
namespace Bss\GeoIPAutoSwitchStore\Model\Config\Backend;

class CheckCookieNumber extends \Magento\Framework\App\Config\Value
{
    /**
     * Plugin before Save
     *
     * @return $this
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');

        if ($this->getValue() == '') {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is required.'));
        } elseif (!is_numeric($this->getValue())) {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is not a number.'));
        } elseif ((int)$this->getValue() < 0 || (int)$this->getValue() > 3650) {
            throw new \Magento\Framework\Exception\ValidatorException(__($label .
                ' at least 1 day and at most 3650 days (10 years).'));
        }

        $this->setValue((int)($this->getValue()));

        return parent::beforeSave();
    }
}
