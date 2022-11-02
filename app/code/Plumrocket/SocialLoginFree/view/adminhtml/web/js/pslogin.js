/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

require([
    'jquery',
    'jquery/ui',
    'mage/adminhtml/events'
], function ($) {
    // 'use strict';

    $(function () {
        if ($('#psloginfree_general_enable').size()) {
            // Disable empty image fields.
            if (typeof varienGlobalEvents != undefined) {
                varienGlobalEvents.attachEventHandler('formSubmit', function () {
                    $('[id$=icon_btn], [id$=login_btn], [id$=register_btn]').each(function () {
                        var $input = $(this);
                        var canDisable = true;

                        // If is new value.
                        if ($input.val()) {
                            canDisable = false;
                        }

                        // If is set value and not checked "Delete Image".
                        var isImageDelete = $('#' + $input.attr('id') + '_delete');
                        if (isImageDelete.size() != false) {
                            if (isImageDelete.is(':checked')) {
                                canDisable = false;
                            } else {
                                // Remove hidden field, to avoid notice after save.
                                isImageDelete.nextAll('input[type="hidden"]').remove();
                            }
                        }

                        if (canDisable) {
                            $input.attr('disabled', 'disabled');
                        }
                    });
                });
            }
        }

        // Sortable.
        $('ul#sortable-visible').sortable({
            connectWith: "ul",
            receive: function (event, ui) {
                ui.item.attr('id', ui.item.attr('id').replace(ui.sender.data('list'), $(this).data('list')));
            },
            update: function (event, ui) {
                $('#psloginfree_general_sortable').val($('#sortable-visible').sortable('serialize'));
            },
            stop: function (event, ui) {
                if (this.id === 'sortable-visible' && $('#'+ this.id +' li').length < 1) {
                    alert('Sorry, "Visible Buttons" list can not be empty');
                    // return false;
                    $(this).sortable('cancel');
                }
            }
        })
        .disableSelection();

        if ($('#psloginfree_general_sortable_drag_and_drop').css('display') != 'none') {
            if ($('#psloginfree_general_sortable_inherit').length) {
                $('#psloginfree_general_sortable_inherit').on('change', function () {
                    var $sortLists = $('ul#sortable-visible, ul#sortable-hidden');
                    if ($(this).is(':checked')) {
                        $sortLists.sortable({disabled: true});
                    } else {
                        $sortLists.sortable({disabled: false});
                    }
                }).change();
            }
        } else {
            $('#row_psloginfree_general_sortable').hide();
        }

        // Share Url.
        $('#psloginfree_share_page').find('option[value=__invitationsoff__], option[value=__none__]').prop('disabled', true);

        // Alert "Not installed".
        $('.psloginfree-notinstalled').parents('fieldset.config').each(function () {
            var $section = $('#' + this.id + '-head').parents('div.entry-edit-head');
            $section.addClass('psloginfree-notinstalled-section');
            $section.find('a').append('<span class="psloginfree-notinstalled-title">(Not installed)</span>');
        });

        // Callback URL.
        $('.psloginfree-callbackurl-autofocus').on('focus click', function () {
            $(this).select();
        })
        .each(function (n, item) {
            var $item = $(item);
            if ($item.val().indexOf('http://') >= 0) {
                $item.next('p.note').find('span span').css('color', 'red');
            }
        });
    });
});
