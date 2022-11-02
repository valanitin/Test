/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/action/login',
    'Magento_Customer/js/model/customer',
    'mage/validation',
    'Magento_Checkout/js/model/authentication-messages',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, Component, loginAction, customer, validation, messageContainer, fullScreenLoader) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
        isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
        registerUrl: checkoutConfig.registerUrl,
        forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
        autocomplete: checkoutConfig.autocomplete,
        buttonLabel: checkoutConfig.buttonLabel,
        backgroundColor: checkoutConfig.backgroundColor,
        buttonImageUrl: checkoutConfig.buttonImage,
        appleIconUrl: checkoutConfig.appleIcon,
        buttonLayout: checkoutConfig.buttonLayout,
        appleButtonClass: checkoutConfig.appleButtonClass,
        defaults: {
            template: 'Zealousweb_AppleLogin/authentication'
        },

        /**
         * Is login form enabled for current customer.
         *
         * @return {Boolean}
         */
        isActive: function () {
            return !customer.isLoggedIn();
        },

        /**
         * Provide login action.
         *
         * @param {HTMLElement} loginForm
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if ($(loginForm).validation() &&
                $(loginForm).validation('isValid')
            ) {
                fullScreenLoader.startLoader();
                loginAction(loginData, checkoutConfig.checkoutUrl, undefined, messageContainer).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        },

        /**
         * Provide social login
         *
         */
        signinwithapple: function () {
            var authorizationUrl = window.checkoutConfig.authorizationUrl;
            var redirectionUrl = window.checkoutConfig.redirectionUrl;

            var isiPad = navigator.userAgent.match(/iPad/i) != null;
            var isiPhone = navigator.userAgent.match(/iPhone/i) != null;
            if (isiPad || isiPhone) {
                window.open(redirectionUrl);
            }else{
                window.open(authorizationUrl,'popup','width=600,height=600');  
            } 
            return false;
        },

        /**
         * Is active apple login extension
         *
         */
        isEnabled: function () {
            return window.checkoutConfig.isActive;
        },

        /**
         * Is show button on checkout page.
         *
         */
        isShowButtonOnCheckout: function () {
            return window.checkoutConfig.is_show_button_on_checkout;
        },

        /**
         * Get display type
         *
         */
        isDisplayTypeButton: function () {
            if(window.checkoutConfig.displayType == '1') {
                return true;
            }
            return false;
        }
    });
});
