/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, Component, additionalValidators) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Meetanshi_SavedCards/payment/savedcards',
                active: false
            },

            placeOrderHandler: null,
            validateHandler: null,

            getCode: function () {
                return 'savedcards';
            },

            isActive: function () {
                return true;
            },

            initObservable: function () {
                this._super()
                    .observe('active');

                return this;
            },

            context: function () {
                return this;
            },

            getSavedCardsLogoUrl: function () {
                return window.checkoutConfig.savedcards_imageurl;
            },

            getSavedCardsInstructions: function () {
                return window.checkoutConfig.savedcards_instructions;
            },

            isShowLegend: function () {
                return true;
            },
            getData: function () {
                var data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'card_holder_name': $("#savedcards_card_holder_name").val(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber()
                    }
                };
                data['additional_data'] = _.extend(data['additional_data'], this.additionalData);
                return data;
            },

            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },

            placeOrder: function (data, event) {
                var self = this;
                if (event) {
                    event.preventDefault();
                }
                if (this.validateHandler() && additionalValidators.validate()) {
                    return self._super();
                }
                return false;
            }

        });
    }
);
