/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function ($, Component, customerData) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            var self = this;

            this._super();

            this.wishlist = customerData.get('wishlist');

            this.initWishProduct();

            this.wishlist.subscribe(function () {
                self.initWishProduct();
            }, this);
        },

        initWishProduct : function () {
            var items = this.wishlist().productIds;

            if (items != undefined) {
                $('[data-action=add-to-wishlist]').each(function (index, link) {
                    var linkData = $(link).data('post').data.product;

                    if (items.indexOf(linkData) !== -1) {
                        if (!$(link).hasClass('active')) {
                            $(link).addClass('active');
                        }
                    }
                });
            }
        }
    });
});
