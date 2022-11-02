/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define(
    [
        'jquery',
        'mage/translate',
        'jquery/ui',
        'mage/validation/validation'
    ], function ($) {
        'use strict';

        $.widget(
            'tigren.ajaxSuite', {
                options: {
                    initConfig: {},
                    popupWrapper: null,
                    popup: null,
                    popupForm: null,
                    close: null,
                    popupBlank: null,
                    formKey: null,
                    formKeyInputSelector: 'input[name="form_key"]',
                    popupWrapperSelector: '#mb-ajaxsuite-popup-wrapper',
                    popupSelector: '#mb-ajaxsuite-popup',
                    popupBlankSelector: '#mb-ajaxsuite-blank',
                    closePopupButtonSelector: '#mb-ajaxsuite-close',
                    ajaxSuite: {
                        processStart: 'processStart',
                        processStop: 'processStop',
                        enabled: null,
                        popupTTL: null,
                        animation: null,
                        backgroundColor: '#ededed',
                        headerBackgroundColor: '#400b8f',
                        headerSuccessColor: '#00ff40',
                        headerErrorColor: '#ff002f',
                        headerTextColor: '#fff',
                        buttonTextColor: '#fff',
                        buttonBackgroundColor: '#006bb4'
                    }
                },

                _create: function () {
                    this._bind();
                },

                _bind: function () {
                    this.createElements();
                    this.initEvents();
                },

                createElements: function () {
                    this.options.popupWrapper = $(this.options.popupWrapperSelector);
                    this.options.popupBlank = $(this.options.popupBlankSelector);
                    this.options.close = $(this.options.closePopupButtonSelector);
                    this.options.popup = $(this.options.popupSelector);
                    this.createColorBG();
                },

                createColorBG: function () {
                    var colorBackground = this.options.ajaxSuite.backgroundColor;
                    this.options.popupWrapper.css('background-color', colorBackground);
                },

                initEvents: function () {
                    var self = this;
                    $(document).on(
                        'touchstart click', self.options.closePopupButtonSelector, function () {
                            self.closePopup();
                        }
                    ).on(
                        'keyup', function (e) {
                            if (e.keyCode == 27) {
                                self.closePopup();
                            }
                        }
                    );

                },

                animationSlide: function (section) {
                    var self = this;
                    var animation = this.options.ajaxLogin.slideAnimation;
                    switch (animation) {
                        case 'show':
                            section.show();
                            break;
                        case 'fade_fast':
                            section.fadeIn(1000);
                            break;
                        case 'fade_medium':
                            section.fadeIn(2000);
                            break;
                        case 'fade_slow':
                            section.fadeIn(3000);
                            break;
                        case 'slide_fast':
                            section.slideDown(1000);
                            break;
                        case 'slide_medium':
                            section.slideDown(2000);
                            break;
                        case 'slide_slow':
                            section.slideDown(3000);
                            break;
                        default:
                            section.show();
                            break;
                    }
                },

                animationPopup: function () {
                    var animation = this.options.ajaxSuite.animation;
                    switch (animation) {
                        case 'fade':
                            this.options.popupWrapper.fadeIn(2000);
                            break;
                        case 'slide_top':
                            this.options.popupWrapper.show();
                            break;
                        case 'slide_bottom':
                            this.options.popupWrapper.show();
                            break;
                        case 'slide_left':
                            this.options.popupWrapper.show();
                            break;
                        case 'slide_right':
                            this.options.popupWrapper.show();
                            break;
                        default:
                            this.options.popupWrapper.show();
                            break;
                    }

                    this.options.popupBlank.fadeIn('slow');
                },

                isLoaderEnabled: function () {
                    return (this.options.ajaxSuite.processStart && this.options.ajaxSuite.processStop);
                },

                showElement: function (elmSelector) {
                    this.options.popup.children().hide();
                    this.options.popup.children(elmSelector).show();
                    this.animationPopup();
                },

                makeColor: function () {
                    var errorSelector = $('.error-content .error-message'),
                        titleSelector = $('.mb-login-popup-title');
                    if (errorSelector.length) {
                        titleSelector.addClass('error');
                    } else {
                        titleSelector.addClass('success');
                    }
                    if (titleSelector.hasClass('success')) {
                        this.options.popupWrapper.find('.mb-login-popup-title').
                            css('background-color', this.options.ajaxSuite.headerSuccessColor);
                    } else if (titleSelector.hasClass('error')) {
                        this.options.popupWrapper.find('.mb-login-popup-title').
                            css('background-color', this.options.ajaxSuite.headerErrorColor);
                    }
                    this.options.popupWrapper.find('.mb-login-popup-title strong').
                        css('color', this.options.ajaxSuite.headerTextColor);
                    this.options.popupWrapper.find('button').css('color', this.options.ajaxSuite.buttonTextColor);
                    this.options.popupWrapper.find('button').
                        css('background-color', this.options.ajaxSuite.buttonBackgroundColor);
                    this.options.popupWrapper.find('button').
                        css('border-color', this.options.ajaxSuite.buttonBackgroundColor);
                },

                autoClosePopup: function (wrapper) {
                    var self = this;
                    if (self.options.ajaxSuite.popupTTL && wrapper.find('.ajaxsuite-autoclose-countdown').length > 0) {
                        var ajaxsuite_autoclose_countdown = setInterval(
                            function () {
                                var leftTimeNode = wrapper.find('.ajaxsuite-autoclose-countdown');
                                var leftTime = parseInt(leftTimeNode.text()) - 1;
                                leftTimeNode.text(leftTime);
                                if (leftTime <= 0) {
                                    clearInterval(ajaxsuite_autoclose_countdown);
                                    self.closePopup();
                                }
                            }, 1000
                        );
                        wrapper.find('.mb-ajaxsuite-close').click(
                            function (event) {
                                clearInterval(ajaxsuite_autoclose_countdown);
                            }
                        );
                        $(self.options.closePopupButtonSelector).click(
                            function () {
                                clearInterval(ajaxsuite_autoclose_countdown);
                            }
                        );
                    }
                },

                closePopup: function () {
                    this.options.popupWrapper.hide();
                    this.options.popupBlank.hide();
                }
            }
        );

        return $.tigren.ajaxSuite;
    }
);
