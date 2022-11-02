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
    'jquery',
    'mage/template',
    'underscore',
    'Bss_GeoIPAutoSwitchStore/js/jquery.magnific-popup.min'
], function ($, template, _) {
    $.widget('bss.bss_config', {
        options: {
            tmplPopup: '#popup-content-template',
            tmplOption: '#select-option-template',
            fourSpace: '&nbsp;&nbsp;&nbsp;&nbsp;',
            eightSpace: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            events: []
        },
        _create: function () {
            var options = this.options;
            var ajaxUrl = options.ajaxUrl;
            var currentUrl = options.currentUrl;
            var currentPath = options.currentPath;
            var _this = this;
            $(document).ready(function() {
                $.ajax({
                    showLoader: false,
                    url: ajaxUrl,
                    data : {
                        current_url: currentUrl,
                        current_path: currentPath
                    },
                    type: "POST",
                    dataType: 'json',
                    complete: function(response) {
                        var result = response.responseText;
                        result = JSON.parse(result);
                        if (result.status) {
                            var element_popup = _this.createPopupElements(result);
                            $(".popup_geoip_content").show();
                            $(".popup_geoip_content").html(element_popup);
                            openPopup();

                            $(".btn-geoip_button").click(function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                var events = _this.options.events;
                                var storeSelected = $('select.selector_bss-store-selector').val();
                                if (undefined !== events[storeSelected]) {
                                    window.location.href = events[storeSelected];
                                }
                                return false;
                            });
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            });

            function openPopup() {
                $.magnificPopup.open({
                    items: {
                        src: $(".popup_geoip_content"),
                        type: 'inline'
                    },
                    type: 'image',
                    closeOnBgClick: false,
                    scrolling: false,
                    preloader: true,
                    tLoading: '',
                    callbacks: {
                        close: function() {
                            $('.mfp-preloader').css('display', 'none');
                            $(".popup_geoip_content").hide();
                        },
                        open: function() {
                            $(".popup_geoip_content").closest('.mfp-wrap').addClass('bss_geoip_popup_wrap');
                            $(".popup_geoip_content").closest('.mfp-wrap').prev().addClass('bss_geoip_popup_bg');
                        }
                    }
                });
            }
        },
        createPopupElements: function (result) {
            var _this = this,
                country_name = result.dataCountry,
                store_name = result.dataStoreName,
                data_post = result.dataPost,
                button = result.dataButton,
                message = result.dataMessage;
            message = message.replace(/\[country]/g, country_name);
            message = message.replace(/\[store_name]/g, store_name);

            var optionElements = template(_this.options.tmplOption);
            var optionVars = _this._makeOptionVars(result);
            var optionHtml = '';
            _.each(optionVars, function (opt) {
                optionHtml += optionElements(opt);
            });

            var data = {
                message: message,
                urlPost: data_post,
                buttonLabel: button,
                options: optionHtml
            };
            var tmplPopupElements = template(_this.options.tmplPopup);

            return tmplPopupElements(data);
        },
        _makeOptionVars: function (result) {
            var vars = [],
                _this = this,
                unsetWebsites = [],
                unsetGroups = [];

            if (undefined !== result.selectors) {
                var websites = result.selectors;
                _.each(websites, function (website, wsCode) {
                    var removeThisWebsite = true;
                    vars.push({
                        'label': website.info.name,
                        'value': wsCode,
                        'level': 1,
                        'disabled': true,
                        'website': wsCode
                    });
                    _.each(website.groups, function (group, grpCode) {
                        var removeThisGroup = true;
                        vars.push({
                            'label': _this.options.fourSpace + group.info.name,
                            'value': wsCode + '_' + grpCode,
                            'level': 2,
                            'disabled': true,
                            'website': wsCode,
                            'group': grpCode
                        });
                        _.each(group.stores, function (store, strCode) {
                            if (!_this.isDisabledStore(store.data)) {
                                removeThisWebsite = false;
                                removeThisGroup = false;
                                vars.push({
                                    'label': _this.options.eightSpace + store.name,
                                    'value': wsCode + '_' + grpCode + '_' + strCode,
                                    'level': 3,
                                    'disabled': _this.isDisabledStore(store.data),
                                    'selected': store.selected,
                                    'website': wsCode,
                                    'group': grpCode
                                });
                                _this.options.events[wsCode + '_' + grpCode + '_' + strCode] = store.data.data.data_post;
                            }
                        });
                        if (removeThisGroup) {
                            unsetGroups.push(grpCode);
                        }
                    });
                    if (removeThisWebsite) {
                        unsetWebsites.push(wsCode);
                    }
                });
            }

            return _this.serializeWebsiteBeforeRender(vars, unsetWebsites, unsetGroups);
        },
        /**
         * Check disable store
         * @param storeData
         * @returns {boolean}
         */
        isDisabledStore: function (storeData) {
            if (undefined !== storeData.data && undefined !== storeData.data.data_post) {
                return false;
            }
            return true;
        },
        /**
         * Update logic
         * If website has all disabled store view.
         * Then unset website from selector
         * @param vars
         * @param unsetWebsites
         * @param unsetGroups
         */
        serializeWebsiteBeforeRender: function (vars, unsetWebsites, unsetGroups) {
            var varsAfter = [],
                varsRenderer = [];
            _.each(vars, function (data, idx) {
                if ((undefined !== data.website && unsetWebsites.indexOf(data.website) === -1)) {
                    varsAfter.push(data);
                }
            });
            _.each(varsAfter, function (data, idx) {
                if ((undefined === data.group || unsetGroups.indexOf(data.group) === -1)) {
                    varsRenderer.push(data);
                }
            });
            return varsRenderer;
        }
    });
    return $.bss.bss_config;
});
