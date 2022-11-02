define([
    'jquery'
], function ($) {
    "use strict";
    return function (config, element) {
        $(element).click(function () {
            var form = $(config.form);
            var baseUrl = form.attr('action');
            var buynowUrl='';
            if(baseUrl.includes('checkout')){
                buynowUrl = baseUrl.replace('checkout/cart/add', 'buynow/cart/add');
            }
            if(baseUrl.includes('wishlist')){
                buynowUrl = baseUrl.replace('wishlist/index/cart', 'buynow/index/cart');
            }
            if(buynowUrl){
                form.attr('action', buynowUrl);
            }
            form.trigger('submit');
            form.attr('action', baseUrl);
            return false;
        });
    }
});
