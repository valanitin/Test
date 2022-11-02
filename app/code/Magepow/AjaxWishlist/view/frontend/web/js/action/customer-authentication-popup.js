
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    'mage/storage',
    'mage/translate',
    'mage/mage',
    'jquery/ui'
], function ($, modal, customerData, storage, $t) {
    'use strict';

    $.widget('ajaxwishlist.customerAuthenticationPopup', {
        options: {
            login: '#customer-popup-login',
            prevLogin: 'a.towishlist'
        },
         _create: function () {
            var self = this,
                loginPopup = $(self.options.login),
                body =  $('body');

            // Show the login form in a popup when clicking on the sign in text
            body.on('click', self.options.prevLogin, function() {
                var authentication_options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    buttons: false,
                    modalClass : 'customer-popup-ajaxwishlist',
                    closed: function(){
                       $('.customer-popup-ajaxwishlist ').remove();
                       body.find('.modals-overlay').css('z-index', '899');
                    }                  
                };

                modal(authentication_options, loginPopup);
                loginPopup.removeClass('_disabled');
                loginPopup.modal('openModal');
                return false;
            });

            this._ajaxSubmit();
        },

        _ajaxSubmit: function() {
            var self = this,
                form = this.element.find('form'),
                inputElement = form.find('input');

            inputElement.keyup(function (e) {
                self.element.find('.messages').html('');
            });

            form.submit(function (e) {
                if (form.validation('isValid')) {
                    if (form.hasClass('form-create-account')) {
                        $.ajax({
                            url: $(e.target).attr('action'),
                            data: $(e.target).serialize(),
                            showLoader: true,
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                self._showResponse(response, form.find('input[name="redirect_url"]').val());
                            },
                            error: function() {
                                self._showFailingMessage();
                            }
                        });
                    } else {
                        var submitData = {},
                            formDataArray = $(e.target).serializeArray();
                        formDataArray.forEach(function (entry) {
                            submitData[entry.name] = entry.value;
                        });
                        $('body').loader().loader('show');
                        storage.post(
                            $(e.target).attr('action'),
                            JSON.stringify(submitData)
                        ).done(function (response) {
                            $('body').loader().loader('hide');
                            self._showResponse(response, form.find('input[name="redirect_url"]').val());
                        }).fail(function () {
                            $('body').loader().loader('hide');
                            self._showFailingMessage();
                        });
                    }
                }
                return false;
            });
        },

        /**
         * Display messages on the screen
         * @private
         */
        _displayMessages: function(className, message) {
            $('<div class="message '+className+'"><div>'+message+'</div></div>').appendTo(this.element.find('.messages'));
        },

        /**
         * Showing response results
         * @private
         * @param {Object} response
         * @param {String} locationHref
         */
        _showResponse: function(response, locationHref) {
            var self = this,
                timeout = 800;
            this.element.find('.messages').html('');
            if (response.errors) {
                this._displayMessages('message-error error', response.message);
            } else {
                this._displayMessages('message-success success', response.message);
            }
            this.element.find('.messages .message').show();
            setTimeout(function() {
                if (!response.errors) {
                    self.element.modal('closeModal');
                    window.location.href = locationHref;
                }
            }, timeout);
        },

        /**
         * Show the failing message
         * @private
         */
        _showFailingMessage: function() {
            this.element.find('.messages').html('');
            this._displayMessages('message-error error', $t('An error occurred, please try again later.'));
            this.element.find('.messages .message').show();
        }
    });

    return $.ajaxwishlist.customerAuthenticationPopup;
});
