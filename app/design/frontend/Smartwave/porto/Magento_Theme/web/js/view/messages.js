/**
* Copyright © Magento, Inc. All rights reserved.
* See COPYING.txt for license details.
*/

/**
* @api
*/
define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'jquery/jquery-storageapi'
    ], function ($, Component, customerData, _) {
        'use strict';

        return Component.extend({
            defaults: {
                cookieMessages: [],
                messages: []                
            },

            /**
            * Extends Component object by storage observable messages.
            */
            initialize: function () {

                this._super();
                this.cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text');
                this.messages = customerData.get('messages').extend({
                    disposableCustomerData: 'messages'
                });

                // Force to clean obsolete messages
                if (!_.isEmpty(this.messages().messages)) {
                    customerData.set('messages', {});
                }


                $.cookieStorage.set('mage-messages', '');
            },
            hideMessage : function (success,error) {
                var successMessage= "discard";
                var errotMessage= "discard";
                if(success == 1){
                    successMessage = "success";
                }
                if(error == 1){
                    errotMessage = "error";
                }
                var hideclass = $(".messages > .message").attr("class");
                var str1 = hideclass;
                if(str1.indexOf(successMessage) == -1 && str1.indexOf(errotMessage) == -1){

                    $(".messages > .message").hide();
                
            }
                /* var array = $.map(this.messages().messages, function(value, index){
                return [value];
                });
                console.log(array);
                $.each(array, function (index, value) {

                $.each(value, function (index, value) {
                alert(value);
                if(value == "success")
                {
                return true;
                }

                })    
                });     */
                //console.log(this.messages());
            },
            
        });
});
