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
            var ajaxUrlDownload = options.ajaxUrlDownload;
            var ajaxUrlExtract = options.ajaxUrlExtract;
            $(document).ready(function() {

                var count = 0;
                $('#collect_button').click(function () {
                    count++;
                    if (count > 1) return false;
                    $('button#collect_button').css({"background": "#f0f0f0", "border-color": "#adadad","color": "#adadad","cursor": "no-drop"});
                    $('.download_geoip').css("display","inline");
                    $('img.processing_download').css("display","inline");
                    $.ajax({
                        showLoader: false,
                        url: ajaxUrlDownload,
                        data : {
                            type: 'ipv4'
                        },
                        type: "POST",
                        dataType: 'json',
                        complete: function(response) {            
                            var result = response.responseText;
                            try {
                                result = JSON.parse(result);
                                if (result['status'] == 'Done') {
                                    $('img.processing_download').css("display","none");
                                    $('img.collected_download').css("display","inline");
                                    $('p.download_success').html("Download Success");
                                    $('.unzipping').css("display","inline");
                                    $('img.processing_unzip').css("display","inline");
                                    $.ajax({
                                        showLoader: false,
                                        url: ajaxUrlExtract,
                                        data : {
                                            type: 'ipv4'
                                        },
                                        type: "POST",
                                        dataType: 'json',
                                        complete: function(response) {            
                                            var result = response.responseText;

                                            try {
                                                result = JSON.parse(result);
                                                if (result == 'Done') {
                                                    $('img.processing_unzip').css("display","none");
                                                    $('img.collected_unzip').css("display","inline");
                                                    $('p.unzip_success').html("Extract Success");
                                                    $('.deleting').css("display","inline");
                                                    $('.done_import').css("display","inline");
                                                    $('.done_import').html("The Import progress will be run automatically via Cron. Please make sure that Cron is enabled. The system will send notification when the Import progress is finished.");
                                                } else {
                                                    $('.error_import').css("display","inline");
                                                    $('.error_import').html("Error while Extract file from the Host");
                                                }
                                            } catch (err) {
                                                $('.error_import').css("display","inline");
                                                $('.error_import').html("Error while Extract file from the Host");
                                            }   
                                        },
                                        error: function() {
                                            $('.error_import').css("display","inline");
                                            $('.error_import').html("Error while Extract file from the Host");
                                        }
                                    });

                                } else {
                                    $('.error_import').css("display","inline");
                                    $('.error_import').html("Error while Download file from the Host");
                                }
                            } catch (err) {
                                $('.error_import').css("display","inline");
                                $('.error_import').html("Error while Download file from the Host");
                            }
                            
                        },
                        error: function() {
                            $('.error_import').css("display","inline");
                            $('.error_import').html("Error while Download file from the Host");
                        }
                    });
                });

            });
        }
    });
    return $.bss.bss_config;
});
