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

class GeoIpMaxMindIPv6 extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init('bss_geoip_maxmind_v6', 'geoip_id');
    }

    /**
     * @param string $network
     * @param string $geoipValue
     */
    public function saveValueIPv6($network, $geoipValue)
    {
        $connection = $this->getConnection();
        $bind = [
            'network' => $network,
            'geoip_value' => $geoipValue
        ];
        $connection->insert($this->getTable('bss_geoip_maxmind_v6'), $bind);
    }

    /**
     * @param string $geoipId
     */
    public function deleteValueIPv6($geoipId)
    {
        $this->getConnection()->delete($this->getTable('bss_geoip_maxmind_v6'), ['geoip_id=?' => $geoipId]);
    }
}
