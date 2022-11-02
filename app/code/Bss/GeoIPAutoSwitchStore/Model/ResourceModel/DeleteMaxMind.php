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

class DeleteMaxMind
{

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpMaxMind
     */
    private $geoIpMaxMind;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * DeleteMaxMind constructor.
     * @param \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpMaxMind $geoIpMaxMind
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Model\ResourceModel\GeoIpMaxMind $geoIpMaxMind,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->geoIpMaxMind = $geoIpMaxMind;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteValue()
    {
        $writeAdapter = $this->resourceConnection->getConnection('core_write');
        //Delete Direct SQL
        $writeAdapter->delete($this->getTableName('bss_geoip_maxmind_v4'));
        $writeAdapter->delete($this->getTableName('bss_geoip_maxmind_v6'));
        $writeAdapter->delete($this->getTableName('bss_geoip_maxmind_locations'));
    }

    /**
     * @param string $entity
     * @return bool|mixed
     */
    protected function getTableName($entity)
    {
        if (!isset($this->tableNames[$entity])) {
            try {
                $this->tableNames[$entity] = $this->resourceConnection->getTableName($entity);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->tableNames[$entity];
    }
}
