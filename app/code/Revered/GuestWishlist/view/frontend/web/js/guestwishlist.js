/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */

define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'jquery/jquery.cookie'
], function  ($, customerData, modal, $t) {
    'use strict';

    $.widget('reveredtech.guestwishlist', {
        options: {
            wishlistUrl: '',
            customerData: customerData,
            modal: modal,
            translate: $t,
            wishlist: [],
            addWish: '[data-action=add-to-wishlist]',
            linkWish: '#wishlist-link',
            modalWish: '#mini-wishlist-modal'
        },

        /** @inheritdoc */
        _create: function () {
            this.init();
        },

        init: function () {
            var guestObj = this;

            $(window).ready(
                function () {
                    guestObj.initControls();
                    guestObj.build(false, guestObj);
                }
            );
        },

        initControls: function () {
            var guestObj = this;

            $(document.body)
                .on('click', guestObj.options.addWish, function (event) {
                    if (!$(this).hasClass('updated')) {
                        event.stopImmediatePropagation();
                        event.preventDefault();
                        event.stopPropagation();

                        if (!$(this).hasClass('active')) {
                            $(this).addClass('active');
                            guestObj.addProduct($(this).data('post'));
                        } else {
                            $(this).removeClass('active');
                            guestObj.removeProduct($(this).data('post'));
                        }
                        return false;
                    }
                });

            if ($(guestObj.options.modalWish).length) {
                $(guestObj.options.modalWish).removeClass('no-display');

                var popupWishlist = guestObj.options.modal({
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: guestObj.options.translate('My Wish List'),
                    buttons: [{
                        text: guestObj.options.translate('Go to Wish List'),
                        class: 'action primary',
                        click: function () {
                            window.location.href = guestObj.options.wishlistUrl;
                        }
                    }]
                }, $(guestObj.options.modalWish));
            }

            $(document.body).on('click', guestObj.options.linkWish, function () {
                $(guestObj.options.modalWish).modal('openModal');

                return false;
            });
        },

        build: function (response, guestObj) {
            if (response != false) {
                var sections = ['wishlist'];

                guestObj.options.customerData.invalidate(sections);
                guestObj.options.customerData.reload(sections, true);
            }
        },

        addProduct: function (postData) {
            var $this = this;

            postData.data.form_key = $.cookie('form_key');

            $.post(postData.action, postData.data, function (response) {
                $this.build(response, $this);
            });
        },

        removeProduct: function (postData) {
            var $this = this;

            postData.data.form_key = $.cookie('form_key');

            var removeUrl = postData.action;

            removeUrl = removeUrl.replace('/add', '/removeProduct');
            removeUrl = removeUrl.replace('/wishlist', '/guestwishlist');

            $.post(removeUrl, postData.data, function (response) {
                $this.build(response, $this);
            });
        }
    });

    return $.reveredtech.guestwishlist;
});
