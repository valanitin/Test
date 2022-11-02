/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/form/element/email',
    'ko'
], function ($, Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Zealousweb_AppleLogin/form/element/email',
        },
        buttonLabel: ko.observable(window.checkoutConfig.buttonLabel),
        backgroundColor: ko.observable(window.checkoutConfig.backgroundColor),
        buttonImageUrl: ko.observable(window.checkoutConfig.buttonImage),
        appleIconUrl: ko.observable(window.checkoutConfig.appleIcon),
        buttonLayout: ko.observable(window.checkoutConfig.buttonLayout),
        appleButtonClass: ko.observable(window.checkoutConfig.appleButtonClass),

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
