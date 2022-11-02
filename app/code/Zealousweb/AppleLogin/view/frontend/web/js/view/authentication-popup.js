/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/action/login',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/authentication-popup',
    'mage/translate',
    'mage/url',
    'Magento_Ui/js/modal/alert',
    'mage/validation'
], function ($, ko, Component, loginAction, customerData, authenticationPopup, $t, url, alert) {
    'use strict';

    return Component.extend({
        registerUrl: window.authenticationPopup.customerRegisterUrl,
        forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
        autocomplete: window.authenticationPopup.autocomplete,
        modalWindow: null,
        isLoading: ko.observable(false),
        buttonLabel: ko.observable(window.checkoutConfig.buttonLabel),
        backgroundColor: ko.observable(window.checkoutConfig.backgroundColor),
        buttonImageUrl: ko.observable(window.checkoutConfig.buttonImage),
        appleIconUrl: ko.observable(window.checkoutConfig.appleIcon),
        buttonLayout: ko.observable(window.checkoutConfig.buttonLayout),
        appleButtonClass: ko.observable(window.checkoutConfig.appleButtonClass),

        defaults: {
            template: 'Zealousweb_AppleLogin/authentication-popup'
        },

        /**
         * Init
         */
        initialize: function () {
            var self = this;

            this._super();
            url.setBaseUrl(window.authenticationPopup.baseUrl);
            loginAction.registerLoginCallback(function () {
                self.isLoading(false);
            });
        },

        /** Init popup login window */
        setModalElement: function (element) {
            if (authenticationPopup.modalWindow == null) {
                authenticationPopup.createPopUp(element);
            }
        },

        /** Is login form enabled for current customer */
        isActive: function () {
            var customer = customerData.get('customer');

            return customer() == false; //eslint-disable-line eqeqeq
        },

        /** Show login popup window */
        showModal: function () {
            if (this.modalWindow) {
                $(this.modalWindow).modal('openModal');
            } else {
                alert({
                    content: $t('Guest checkout is disabled.')
                });
            }
        },

        /**
         * Provide login action
         *
         * @return {Boolean}
         */
        login: function (formUiElement, event) {
            var loginData = {},
                formElement = $(event.currentTarget),
                formDataArray = formElement.serializeArray();

            event.stopPropagation();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (formElement.validation() &&
                formElement.validation('isValid')
            ) {
                this.isLoading(true);
                loginAction(loginData);
            }

            return false;
        },

        /**
         * Provide social login
         *
         */
        signinwithapple: function () {
            var authorizationUrl = window.authenticationPopup.authorizationUrl;
            var redirectionUrl = window.authenticationPopup.redirectionUrl;

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
            return window.authenticationPopup.isActive;
        },

        /**
         * Is show button on checkout page.
         *
         */
        isShowButtonOnCheckout: function () {
            return window.checkoutConfig.is_show_button_on_checkout;
        },

        /**
         * Is display type button
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
