/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    var PRAMP = new PRAMP_Class();

    function PRAMP_Class() {
        var self = this;

        self.parseLink = function (modal) {
            var input = modal.focussedElement.parentElement.querySelector('input');
            var image = modal.focussedElement.parentElement.querySelector('img');
            var label = modal.focussedElement.parentElement.querySelector('.control-value');
            if (typeof input !== 'undefined' && input.value && input.value.indexOf('/___directive/')) {
                var result = /___directive\/(.*?),*\//ig.exec(input.value);
                if (result) {
                    var declaration = self.decodeDeclaration(result[1]);
                    var imagePath = /url="(.*?)"/ig.exec(declaration)[1];
                    input.value = imagePath;
                    image.src = image.dataset.mediaUrl + imagePath;
                    label.innerText = imagePath;
                }
            }
        };

        self.insertSize = function () {
            $("img.amp-banner").load(function () {
                var selectorWidth = "input[id*='_image_width']";
                var selectorHeight = "input[id*='_image_height']";

                if (! ($(selectorWidth).val() || $(selectorHeight).val())) {
                    var scale = $(this).width()/$(this).height();
                    var widthImg = 800;
                    var heightImg = parseInt(widthImg/scale);

                    $(selectorWidth).val(widthImg);
                    $(selectorHeight).val(heightImg);
                }
            });
        };

        self.initSliderConfiguration = function () {
            $('select[name*="show_slide"]').each(function (index, select) {
                var regex = /[+-]?\d+(?:\.\d+)?/g;
                var match = regex.exec(select.name);
                if (match && match[0]) {
                    var div = document.querySelector('div[class*="image' + match[0] + '"]');
                    $(select).on('change', function () {
                        $(div).toggle();
                        div.querySelector('input').classList.toggle('required-entry');
                    });
                    if ('0' === select.value) {
                        $(div).hide();
                        div.querySelector('input').classList.toggle('required-entry');
                    }
                }
            });
        };

        self.decodeDeclaration = function (declaration) {
            return Base64.decode(decodeURIComponent(declaration).replace(/,/g, '='));
        };
    }

    window.PRAMP = PRAMP;

    return PRAMP;
});
