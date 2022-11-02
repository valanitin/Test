<?php
/**
 * Magentizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magentizer.com license that is
 * available through the world-wide-web at this URL:
 * https://www.Magentizer.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magentizer
 * @package     Magentizer_GeoIP
 * @copyright   Copyright (c) Magentizer (https://www.Magentizer.com/)
 * @license     https://www.Magentizer.com/LICENSE.txt
 */

namespace Magentizer\GeoIP\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package Magentizer\GeoIP\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'geoip';

    /**
     * @var Address
     */
    protected $_addressHelper;

    /**
     * @return Address
     */
    public function getAddressHelper()
    {
        if (!$this->_addressHelper) {
            $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$this->_addressHelper = $object_manager->get('\Magentizer\GeoIP\Helper\Address');
        }

        return $this->_addressHelper;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getDownloadPath($store = null)
    {
        $licenseKey = $this->getAddressHelper()->getLicenseKey();
        return 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key='.$licenseKey.'&suffix=tar.gz';
    }
}
