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
namespace Bss\GeoIPAutoSwitchStore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BotSearch implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Google',
                'label' => __('Google')
            ],
            [
                'value' => 'msnbot',
                'label' => __('MSN')
            ],
            [
                'value' => 'Rambler',
                'label' => __('Rambler')
            ],
            [
                'value' => 'Yahoo',
                'label' => __('Yahoo')
            ],
            [
                'value' => 'facebookexternalhit',
                'label' => __('Facebook')
            ],
            [
                'value' => 'ASPSeek',
                'label' => __('ASPSeek')
            ],
            [
                'value' => 'AbachoBOT',
                'label' => __('AbachoBOT')
            ],
            [
                'value' => 'Accoona',
                'label' => __('Accoona')
            ],
            [
                'value' => 'AcoiRobot',
                'label' => __('AcoiRobot')
            ],
            [
                'value' => 'CrocCrawler',
                'label' => __('CrocCrawler')
            ],
            [
                'value' => 'Dumbot',
                'label' => __('Dumbot')
            ],
            [
                'value' => 'FAST-WebCrawler',
                'label' => __('FAST-WebCrawler')
            ],
            [
                'value' => 'GeonaBot',
                'label' => __('GeonaBot')
            ],
            [
                'value' => 'Gigabot',
                'label' => __('Gigabot')
            ],
            [
                'value' => 'Lycos',
                'label' => __('Lycos')
            ],
            [
                'value' => 'MSRBOT',
                'label' => __('MSRBOT')
            ],
            [
                'value' => 'Scooter',
                'label' => __('Scooter')
            ],
            [
                'value' => 'Altavista',
                'label' => __('Altavista')
            ],
            [
                'value' => 'IDBot',
                'label' => __('IDBot')
            ],
            [
                'value' => 'eStyle',
                'label' => __('eStyle')
            ],
            [
                'value' => 'Scrubby',
                'label' => __('Scrubby')
            ],
            [
                'value' => 'SeoSiteCheckup',
                'label' => __('SeoSiteCheckup')
            ]
        ];
    }
}
