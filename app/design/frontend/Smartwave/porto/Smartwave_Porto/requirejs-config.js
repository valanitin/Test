var config = {
    paths: {
        'imagesloaded': 'Smartwave_Porto/js/imagesloaded',
        'packery': 'Smartwave_Porto/js/packery.pkgd'
    },
    shim: {
        'imagesloaded': {
            deps: ['jquery']
        },
        'packery': {
            deps: ['jquery']
        },
        'jquery/jquery-migrate': {
            init: function () {
                jQuery.migrateMute = true;
                jQuery.migrateTrace = false;
            }
        }
    }
};
