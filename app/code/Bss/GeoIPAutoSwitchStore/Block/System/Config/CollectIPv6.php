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

namespace Bss\GeoIPAutoSwitchStore\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CollectIPv6 extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Bss_GeoIPAutoSwitchStore::system/config/collect_ipv6.phtml';

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp
     */
    protected $geoIp;

    /**
     * Collect constructor.
     * @param \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp $geoIp
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Helper\UpdateTimeGeoIp $geoIp,
        Context $context,
        array $data = []
    ) {
        $this->geoIp = $geoIp;
        parent::__construct($context, $data);
    }

    /**
     * @return array|bool
     */
    public function getUpdateTimeGeoIp()
    {
        if ($this->geoIp->getSelectingData('last_update_ipv6')) {
            return $this->geoIp->getSelectingData('last_update_ipv6');
        } else {
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getStatusGeoIp()
    {
        if ($this->geoIp->getSelectingData('status_ipv6')) {
            return $this->geoIp->getSelectingData('status_ipv6');
        } else {
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getPercentGeoIp()
    {
        if ($this->geoIp->getSelectingData('percent_ipv6')) {
            return $this->geoIp->getSelectingData('percent_ipv6');
        } else {
            return '0';
        }
    }

    /**
     * Return element html
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'collect_button_ipv6',
                'label' => __('Upgrade Data'),
            ]
        );

        return $button->toHtml();
    }
}
