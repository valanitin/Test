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

class Collect extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Bss_GeoIPAutoSwitchStore::system/config/collect.phtml';

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
     * Remove scope label
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @return array|bool
     */
    public function getUpdateTimeGeoIp()
    {
        if ($this->geoIp->getSelectingData('last_update')) {
            return $this->geoIp->getSelectingData('last_update');
        } else {
            return false;
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
     * @return array|bool
     */
    public function getStatusGeoIp()
    {
        if ($this->geoIp->getSelectingData('status')) {
            return $this->geoIp->getSelectingData('status');
        } else {
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getPercentGeoIp()
    {
        if ($this->geoIp->getSelectingData('percent')) {
            return $this->geoIp->getSelectingData('percent');
        } else {
            return '0';
        }
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrlDownload()
    {
        return $this->getUrl('bss_geoip/system_config/download');
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrlExtract()
    {
        return $this->getUrl('bss_geoip/system_config/extract');
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
                'id' => 'collect_button',
                'label' => __('Upgrade Data'),
            ]
        );

        return $button->toHtml();
    }
}
