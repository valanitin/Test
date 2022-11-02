define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/url',
    'jquery/ui',
    'mage/loader' 
], function($, modal, $t, url) {
    'use strict';

    $.widget('magepow.ajaxWishlist', {

        options: {
            isShowSpinner: true,
            isShowSuccessMessage: true,
            customerLoginUrl: null,
            popupTtl: 10,
            buttonClose: '.action-close',
            formKeyInputSelector: 'input[name="form_key"]',
            popupWrapperSelector: '.show-popup-wapper-wishlist',
            removeFromWishlistSelector: '.block-wishlist .btn-remove',
            wishlistBlockSelector: '#wishlist-view-form',
            addToWishlistButtonSelector: '[data-action="add-to-wishlist"]',
            addToWishlistButtonDisabledClass: 'disabled',
            addToWishlistButtonTextWhileAdding: '',
            addToWishlistButtonTextAdded: '',
            addToWishlistButtonTextDefault: ''
        },

        _create: function() {
            var self = this;
            this._bind();
            this.viewWishlist();
            this.closePopup();
        },

        closePopup: function() {
            $(document).on('click', '#ajaxwishlist_btn_close_popup' , function() {
                $(this.options.buttonClose).trigger('click');
            })
        },
        
        viewWishlist: function(){
            $(document).on('click', "#wishlist_checkout", function() {
                window.location.replace(url.build('wishlist'));
            })
        },
        _bind: function () {
            var self = this,
                selectors = [
                this.options.addToWishlistButtonSelector,
                this.options.removeFromWishlistSelector
            ];

            $('body').on('click', selectors.join(','), $.proxy(this._processViaAjax, this));
        },


        _processViaAjax: function(event) {
            var self   = this,
                post   = $(event.currentTarget).data('post'),
                url    = post.action,
                parent = $(event.currentTarget).parent(),
                data   = $.extend(post.data, {form_key: $(this.options.formKeyInputSelector).val()});
            $.ajax(url, {
                method: 'POST',
                data: data,
                showLoader: this.options.isShowSpinner,
                beforeSend: function () {
                    self.disableAddToWishlistButton(parent);
                },
                success: function () {
                    self.enableAddToWishlistButton(parent);
                },
            }).done($.proxy(this._successHandler, this)).fail($.proxy(this._errorHandler, this));

            event.stopPropagation();
            event.preventDefault();
        },

        _successHandler: function(data) {
            var self = this,
                wishlistPopup = $(self.options.popupWrapperSelector),
                body = $('body');
            if (!data.success && data.error == 'not_logged_in') return;
            $(this.options.wishlistBlockSelector).replaceWith(data.wishlist);
            
            if (this.options.isShowSuccessMessage && data.message) {
                if (!wishlistPopup.length) {
                    body.append('<div class="' + self.options.popupWrapperSelector.replace(/^./, "") +'">'+data.message+'</div>');
                }
                self._showPopup();
                if (self.options.popupTtl) {
                    var wishlist_autoclose_countdown = setInterval(function (wrapper) {
                    var leftTimeNode = $(document).find('#ajaxwishlist_btn_close_popup .wishlist-autoclose-countdown');
                    var leftTime = parseInt(leftTimeNode.text()) - 1;                   
                    leftTimeNode.text(leftTime);
                    if (leftTime <= 0) {
                        $(self.options.buttonClose).trigger('click').fadeOut('slow');
                        clearInterval(wishlist_autoclose_countdown);  
                        }
                    }, 1000);
                }
                self.viewWishlist();
            }
        },

        _showPopup: function() {
            var self = this,
                wishlistPopup = $(self.options.popupWrapperSelector);
            var modaloption = {
                type: 'popup',
                modalClass: 'modal-popup_ajaxwishlist_magepow',
                responsive: true,
                innerScroll: true,
                clickableOverlay: true,
                closed: function(){
                   $('.modal-popup_ajaxwishlist_magepow').remove();
                }
            };
            modal(modaloption, wishlistPopup);
            wishlistPopup.modal('openModal');
        },

        _errorHandler: function () {
            console.warn("Add to the wish list unsuccessful");
        },

        /**
         * @param {String} form
         */
        disableAddToWishlistButton: function (form) {
            var addToWishlistButtonTextWhileAdding = this.options.addToWishlistButtonTextWhileAdding || $t('Adding...'),
                addToWishlistButton = $(form).find(this.options.addToWishlistButtonSelector);

            addToWishlistButton.addClass(this.options.addToWishlistButtonDisabledClass);
            addToWishlistButton.find('span').text(addToWishlistButtonTextWhileAdding);
            addToWishlistButton.attr('title', addToWishlistButtonTextWhileAdding);
        },

        /**
         * @param {String} form
         */
        enableAddToWishlistButton: function (form) {
            var addToWishlistButtonTextAdded = this.options.addToWishlistButtonTextAdded || $t('Added'),
                self = this,
                addToWishlistButton = $(form).find(this.options.addToWishlistButtonSelector);

            addToWishlistButton.find('span').text(addToWishlistButtonTextAdded);
            addToWishlistButton.attr('title', addToWishlistButtonTextAdded);

            setTimeout(function () {
                var addToWishlistButtonTextDefault = self.options.addToWishlistButtonTextDefault || $t('Add to Wishlist');

                addToWishlistButton.removeClass(self.options.addToWishlistButtonDisabledClass);
                addToWishlistButton.find('span').text(addToWishlistButtonTextDefault);
                addToWishlistButton.attr('title', addToWishlistButtonTextDefault);
            }, 1000);
        }


    });

    return $.magepow.ajaxWishlist;
});