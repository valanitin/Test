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

class GeoIpConfig extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init('bss_geo_ip', 'geoip_id');
    }

    /**
     * @param string $value
     * @param string $type
     */
    public function saveValue($value, $type)
    {
        $connection = $this->getConnection();
        $bind = [
            'geoip_type' => $type,
            'geoip_value' => $value
        ];
        $connection->insert($this->getTable('bss_geo_ip'), $bind);
    }

    /**
     * @param string $type
     */
    public function deleteValue($type)
    {
        $this->getConnection()->delete($this->getTable('bss_geo_ip'), ['geoip_type=?' => $type]);
    }

    /**
     * @param string $value
     * @param string $type
     */
    public function updateValue($value, $type)
    {
        $connection = $this->getConnection();
        $where = ['geoip_type IN (?)' => $type];
        $connection->update($this->getTable('bss_geo_ip'), ['geoip_value' => $value], $where);
    }
}
