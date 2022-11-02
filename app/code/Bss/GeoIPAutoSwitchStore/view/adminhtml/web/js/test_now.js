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
define([
    'jquery'
], function ($) {
    'use strict';
    $.widget('bss.bss_config', {
        _create: function () {
            var options = this.options;
            $(document).ready(function() {
                $('#bss_geoip_general_get_url').hide();
                $('#bss_geoip_general_comment').hide();
                $('#bss_collect_button').click(function () {
                    $('#bss_geoip_general_get_url').show();
                    var urlTester = $('#bss_geoip_general_tester_url').val();
                    var ipTester = $('#bss_geoip_general_tester_ip').val();

                    var checkUrl = urlTester.indexOf("?");
                    if (checkUrl !== -1) {
                        var url = urlTester + "&ipTester=" + ipTester;
                    } else {
                        var url = urlTester + "?ipTester=" + ipTester;
                    }
                    $('#bss_geoip_general_get_url').val(url);
                    $('#bss_geoip_general_comment').show();
                });
            });
        }
    });
    return $.bss.bss_config;
});
