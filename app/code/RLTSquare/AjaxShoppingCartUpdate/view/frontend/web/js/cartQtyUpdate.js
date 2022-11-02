define(['jquery'], function ($) {
    "use strict";
    function cartQtyupdate() {
        var form = $('form#form-validate');
        $.ajax({
            url: form.attr('action'),
            data: form.serialize(),
            showLoader: true,
            success: function (res) {
                var parsedResponse = $.parseHTML(res);
                var result = $(parsedResponse).find("#form-validate");
                var sections = ['cart'];

                $("#form-validate").replaceWith(result);

                require(['Magento_Checkout/js/action/get-totals',
                    'Magento_Customer/js/customer-data'], function (getTotalsAction, customerData) {
                    // The mini cart reloading
                    customerData.reload(sections, true);

                    // The totals summary block reloading
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);

                    //Display error if found after jquery
                    var messages = $.cookieStorage.get('mage-messages');
                    if (!_.isEmpty(messages)) {
                        customerData.set('messages', {messages: messages});
                        $.cookieStorage.set('mage-messages', '');
                    }
                });
            },
            error: function (xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
    }

    $(document).on('change', '.custom-qty input', function () {
        cartQtyupdate();
    });
    $(document).on('click', '.custom-qty a', function () {
        cartQtyupdate();
    });
});