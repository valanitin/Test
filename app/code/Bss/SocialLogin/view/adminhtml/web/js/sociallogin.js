
require([
    'jquery',
    'jquery/ui',
    'mage/adminhtml/events'
], function($) {
    $(function () {
        $('ul#sort-social').sortable({
            connectWith: "ul",
            receive: function(event, ui) {
                ui.item.attr('id', ui.item.attr('id').replace(ui.sender.data('list'), $(this).data('list')));
            },
            update: function(event, ui) {
                var sortable = [
                    $('#sort-social').sortable('serialize'),
                ];
                $('#sociallogin_bsbutton_social_login_sort_sociallogin').val( sortable.join('&') );
            }
        })
        .disableSelection();
        $('#row_sociallogin_bsbutton_social_login_sort_sociallogin').hide();
        $('.sociallogin-callbackurl-autofocus').on('focus click', function() {
            var $focus = $(this);
            $focus.select();
        })
    });
});