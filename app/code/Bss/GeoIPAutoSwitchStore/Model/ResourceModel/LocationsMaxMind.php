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
namespace Bss\GeoIPAutoSwitchStore\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LocationsMaxMind extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init('bss_geoip_maxmind_locations', 'local_id');
    }

    /**
     * @param string $geonameId
     * @param string $localeCode
     * @param string $continentCode
     * @param string $continentName
     * @param string $countryIsoCode
     */
    public function saveLocations($geonameId, $localeCode, $continentCode, $continentName, $countryIsoCode)
    {
        $connection = $this->getConnection();
        $bind = [
            'geoname_id' => $geonameId,
            'locale_code' => $localeCode,
            'continent_code' => $continentCode,
            'continent_name' => $continentName,
            'country_iso_code' => $countryIsoCode
        ];
        $connection->insert($this->getTable('bss_geoip_maxmind_locations'), $bind);
    }

    /**
     * @param string $geoipId
     */
    public function deleteLocations($geoipId)
    {
        $this->getConnection()->delete($this->getTable('bss_geoip_maxmind_locations'), ['geoip_id=?' => $geoipId]);
    }
}
