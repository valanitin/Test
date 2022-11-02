/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
    return {
        /**
         * @param {string} linkId
         * @param {string} contentId
         */
        initUpdates: function (linkId, contentId) {
            const link = document.getElementById(linkId);
            const content = document.getElementById(contentId);

            let options = {
                type: 'popup',
                responsive: true,
                title: 'New Updates',
                buttons: [
                    {
                        text: $.mage.__('Close'),
                        class: '',
                        click: function () {
                            this.closeModal();
                        }
                    },
                    {
                        text: $.mage.__('Get New Version'),
                        class: 'secondary',
                        click: function () {
                            window.open(link.href, '_blank').focus();
                            this.closeModal();
                        }
                    },
                ]
            };

            modal(options, $(content));

            link.addEventListener('click', function (e) {
                e.preventDefault();
                $(content).modal('openModal')
            });
        },
    };
});
