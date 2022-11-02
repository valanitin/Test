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

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Magentizer_GeoIP',
    __DIR__
);
