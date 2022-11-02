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
namespace Bss\GeoIPAutoSwitchStore\Helper;

class UpdateTimeGeoIp extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\GeoIpConfigFactory
     */
    private $geoIpConfig;

    /**
     * UpdateTimeGeoIp constructor.
     * @param \Bss\GeoIPAutoSwitchStore\Model\GeoIpConfigFactory $geoIpConfig
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Model\GeoIpConfigFactory $geoIpConfig
    ) {
        $this->geoIpConfig = $geoIpConfig;
    }

    /**
     * @param string $type
     * @return array|bool
     */
    public function getSelectingData($type)
    {
        $result = [];
        $collection = $this->geoIpConfig->create()
        ->getCollection()
        ->addFieldToFilter('geoip_type', $type);
        foreach ($collection->getData() as $value) {
            $result = $value['geoip_value'];
        }
        if (null !== $result) {
            return $result;
        } else {
            return false;
        }
    }
}
