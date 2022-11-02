define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
    ], function ($, $t, modal) {
        'use strict';

        $.widget('magepow.ajaxcart', {
            options: {
                processStart: null,
                processStop : null,
                bindSubmit  : true,
                countDown   : 0,
                showLoader  : true,
                minicartSelector: '[data-block="minicart"]',
                messagesSelector: '[data-placeholder="messages"]',
                productStatusSelector: '.stock.available',
                addToCartButtonSelector: '.action.tocart',
                addAllToCartButtonSelector: '.add-all-tocart',
                addToCartButtonDisabledClass: 'disabled',
                addToCartButtonTextWhileAdding: '',
                addToCartButtonTextAdded: '',
                addToCartButtonTextDefault: '',
                addUrl: '',
                quickview: false,
                isProductView: false,
                isSuggestPopup: false,
                quickViewUrl: ''
            },

            productIdInputName: ["product", "product-id", "data-product-id", "data-product"],

            _create: function () {
                this._initAjaxcart();
                window.ajaxCart = this;
                $('body').on('ajaxcart:refresh', function () {
                    window.ajaxCart._initAjaxcart();
                });
            },

            _initAjaxcart: function () {
                var options = this.options;
                var self = this;

                self.addAllToCart();
                self.element.off('click').on("click", options.addToCartButtonSelector, function(e){
                    if($(this).attr('data-post')) return;// turn off add to cart for wishlist in category page
                    var form = $(this).parents('form').get(0);
                    if($(form).hasClass('reorder')) return;// turn off the recently ordered sidebar in category page
                    e.preventDefault();
     
                    var form = $(this).parents('form').get(0);
                    var data = '';
                    if (form) {
                        var isValid = true;
                        if (options.isProductView || $('body').hasClass('open-quickview')) {
                            try {
                                isValid = $(form).valid();
                            } catch(err) {
                                isValid = true;
                            }
                        }

                        if (isValid) {
                            var oldAction = $(form).attr('action');
                            var serialize = $(form).serialize();
                            var id = self._findId(this, oldAction, form);

                            if ($.isNumeric(id)) {
                                data += 'id=' + id;
                                if (serialize == '') {
                                    $(form).find('input, select').each(function () {
                                        data += "&" + $(this).attr('name') + "=" + $(this).val();
                                    });
                                } else {
                                    data += "&" + serialize;
                                }

                                if (options.quickview) {
                                    window.parent.ajaxCart._sendAjax(options.addUrl, data, oldAction, form);
                                    return false;
                                }

                                self._sendAjax(options.addUrl, data, oldAction, form);
                                return false;
                            }

                            window.location.href = oldAction;
                        }
                    } else {
                        var dataPost = $.parseJSON($(this).attr('data-post'));
                        if (dataPost) {
                            var formKey = $("input[name='form_key']").val();
                            var oldAction = dataPost.action;
                            data += 'id=' + dataPost.data.product + '&product=' + dataPost.data.product + '&form_key=' + formKey + '&uenc=' + dataPost.data.uenc;
                            self._sendAjax(options.addUrl, data, oldAction);
                            return false;
                        } else {
                            var id = self._findId(this);
                            if (id) {
                                e.stopImmediatePropagation();
                                self.quickview(options.quickViewUrl + 'id/' + id);
                                return false;
                            }
                        }
                    }
                });
            },
          
            _findId: function (btn, oldAction, form) {
                var self = this;
                var id = $(btn).attr('data-product-id');
                if($.isNumeric(id)) return id;
                var item = $(btn).closest('li.product-item');
                id = $(item).find('[data-product-id]').attr('data-product-id');
                if ($.isNumeric(id)) return id;

                if (oldAction) {
                    var formData = oldAction.split('/');
                    for (var i = 0; i < formData.length; i++) {
                        if (self.productIdInputName.indexOf(formData[i]) >= 0) {
                            if ($.isNumeric(formData[i + 1])) {
                                id = formData[i + 1];
                            }
                        }
                    }

                    if ($.isNumeric(id)) return id;
                }

                if (form) {
                    $(form).find('input').each(function () {
                        if (self.productIdInputName.indexOf($(this).attr('name')) >= 0) {
                            if ($.isNumeric($(this).val())) {
                                id = $(this).val();
                            }
                        }
                    });

                    if ($.isNumeric(id)) return id;
                }

                var priceBox = $(btn).closest('.price-box.price-final_price');
                id = $(priceBox).attr('data-product-id');

                if ($.isNumeric(id)) return id;

                return false;
            },

            _sendAjax: function (addUrl, data, oldAction, form=false) {
                var body = $('body');
                var options = this.options;
                var self = this;
                if(form){
                    self.disableAddToCartButton(form);
                    data = new FormData(form);
                }
                $.ajax({
                    type: 'post',
                    url: addUrl,
                    data: data,
                    showLoader: options.showLoader,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
						//console.dir(data);
                        var visitedVal = $('#product_addtocart_form').find("input[name='visited']").val();
                        if (data.popup) {
                            if (visitedVal) {
                                $('.mp-product-options').remove();
                                $('p.message').empty();
                                $('p.message').text('Product Added into the cart.');
                                $('.content-ajaxcart').find('.viewcart').text($.mage.__('View Cart')).removeClass('addtocart-wp');
                                $('.content-ajaxcart').find('.wp-product-container').remove();
                                $('.content-ajaxcart').find('.product-option-btn').remove();
                                $('.content-ajaxcart').find('.checkout-buy-now').show();
                            } else {
                                if(!$('#modals_ajaxcart').length) body.append('<div id="modals_ajaxcart" style="display:none"></div>');
                                var _qsModal = $('#modals_ajaxcart');
                                self._showPopup(_qsModal, '<div class="content-ajaxcart">' + data.popup + '</div>');
                            }

                            if (data.error) {
                                $('p.message').empty().text(data.pro_name);
                                $('.content-ajaxcart').find('.viewcart').text($.mage.__('Add To Cart'));
                                $('.content-ajaxcart').find('.checkout-buy-now').hide();
                            } else {
                               // $('p.message').empty().text(data.pro_name+' Added into the cart.');
                                $('.content-ajaxcart').find('.viewcart').text($.mage.__('View Cart')).removeClass('addtocart-wp');
                                $('.content-ajaxcart').find('.wp-product-container').remove();
                                $('.content-ajaxcart').find('.product-option-btn').remove();
                                $('.content-ajaxcart').find('.checkout-buy-now').show();
                            }
                        } else if (data.error && data.view) {
                            /*show Quick View*/
                            var quickView = true;
                            if(form){
                                var addToCartButtonMain = $(form).closest('#product_addtocart_form');
                                if(addToCartButtonMain.length) quickView = false;
                            }
                            if(quickView && data.error_info.search("not available") == -1){
                                if ($.fn.quickview) {
                                    $.fn.quickview({url:options.quickViewUrl  + 'id/' + data['id']});
                                } else {
                                    self.quickview({url:options.quickViewUrl  + 'id/' + data['id']});
                                }
                            } else {
                                self._showPopup(_qsModal, data.error_info);
                            }
                        }

                        if(form) self.enableAddToCartButton(form);
                    },
                    error: function () {
                        window.location.href = oldAction;
                    }
                });
            },

            _showPopup: function (_qsModal, data) {
                var body = $('body');
                self = this;           
                _qsModal.html(data);
                if(!body.hasClass('open-ajaxcart')){
                    self._closePopup();
                    body.addClass('open-ajaxcart');
                    var modals = body.find('.modals-ajaxcart');
                    if(!modals.length){
                        modal({
                            type: 'popup',
                            modalClass: 'modals-ajaxcart',
                            responsive: true,
                            innerScroll: true,
                            buttons: false,
                            closed: function(){
                                body.removeClass('open-ajaxcart');                                       
                            } 
                        }, _qsModal);
                    }
                    _qsModal.modal('openModal');
                    _qsModal.trigger('contentUpdated');
                }
                _qsModal.find('.btn-continue').on('click', function() {
                    self._closePopup();
                });
                /*Show count down*/
                 this._showCountdown(); 
            },

            _closePopup: function(){
                var $body = $('body');
                $body.removeClass('open-minicart-modal open-ajaxcart open-quickview _has-modal');
                $body.find('.modal-popup, .modals-overlay').remove();
                clearInterval(window.ajaxcart_countdown);
            },

            _showCountdown: function () {
                self = this;
                var options = this.options;
                var countDown = options.countDown;
                if(countDown > 0) {
                    window.ajaxcart_countdown = setInterval(function () {
                        countDown -= 1;
                        $('.content-ajaxcart span.countdown').text("(" + countDown + ")");
                        if (countDown <= 0) {
                            self._closePopup();
                        }
                    }, 1000);
                }
            },
            /**
             * @param {String} form
             */
            disableAddToCartButton: function (form) {
                var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...'),
                    addToCartButton = $(form).find(this.options.addToCartButtonSelector);

                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
                addToCartButton.attr('title', addToCartButtonTextWhileAdding);
            },

            /**
             * @param {String} form
             */
            enableAddToCartButton: function (form) {
                var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added'),
                    self = this,
                    addToCartButton = $(form).find(this.options.addToCartButtonSelector);

                addToCartButton.find('span').text(addToCartButtonTextAdded);
                addToCartButton.attr('title', addToCartButtonTextAdded);

                setTimeout(function () {
                    var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');

                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.find('span').text(addToCartButtonTextDefault);
                    addToCartButton.attr('title', addToCartButtonTextDefault);
                }, 1000);
            },
            
            quickview: function () {
                var obj = arguments[0];
                var body = $('body');
                if(!$('#modals_quickview').length) body.append('<div id="modals_quickview" style="display:none"></div>');
                var _qsModal = $('#modals_quickview');
                var quickajax= function(url){
                    $.ajax({
                        url:url,
                        type:'POST',
                        showLoader: true,
                        cache:false,   
                        success:function(data){
                            _qsModal.html('<div class="content-quickview">' + data + '</div>');
                            if(!body.hasClass('open-quickview')){
                                body.addClass('open-quickview');
                                var modalsQuickview = body.find('.modals-quickview');
                                if(!modalsQuickview.length){
                                    modal({
                                        type: 'popup',
                                        modalClass: 'modals-quickview',
                                        responsive: true,
                                        innerScroll: true,
                                        buttons: false,
                                        closed: function(){
                                            body.removeClass('open-quickview');                                       
                                        } 
                                    }, _qsModal);
                                }
                                _qsModal.modal('openModal');
                            }
                            _qsModal.trigger('contentUpdated');                        
                        }
                    });
                    _qsModal.on('fotorama:load', function(){
                        _qsModal.find(".product-view .product-info-main.product-shop").height(_qsModal.find(".product-img-box").height());
                    });
                }
                if(obj.url){
                    quickajax(obj.url)
                } else {
                    $(document).on('click', obj.itemClass, function(e) {
                        e.preventDefault();
                        quickajax($(this).data('url'))
                    });
                }
            },

            addAllToCart: function() {
                var self = this;
                var options = this.options;
                $(document).on("click", options.addAllToCartButtonSelector, function(e){
                    var searchIds = $("input[name='product']").map(function(){return $(this).val();}).get();
                    $.ajax({
                        url: options.addUrl + 'index/allcart',
                        type: 'POST',
                        showLoader: true,
                        cache: false,
                        dataType: 'json',
                        data: {productIds : searchIds.join()},
                        success: function (data) {
                            var _qsModalContent = '<div class="content-ajaxcart">quickview placeholder</div>';
                            if(!$('#modals_ajaxcart').length){
                                $(document.body).append('<div id="modals_ajaxcart" style="display:none">' + _qsModalContent + '</div>');
                            }

                            var _qsModal = $('#modals_ajaxcart .content-ajaxcart');
                            if (data.error) {
                                window.location.replace(options.addUrl +'#scroll');
                            }else{
                                if (data.popup) {
                                    self._showPopup(_qsModal, _qsModalContent, data.popup);
                                }
                            }
                            
                        }
                    });
                })                
            }

        });

    return $.magepow.ajaxcart;
});
