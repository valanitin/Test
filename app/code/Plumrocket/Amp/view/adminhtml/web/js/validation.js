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
 * @copyright   Copyright (c) 201 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

requirejs([
    'jquery',
    'underscore',
    'mage/translate',
    'mage/validation'
], function ($, _) {
    'use strict';

    $.validator.addMethod(
        'validate-gtmscript',
        function (value) {
            return null === value.match(/<script[^>]*>/gm);
        },
        $.mage.__('Please enter only the script used for the tag %1.').replace('%1', _.escape('<body>'))
    );
});
