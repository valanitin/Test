var config = {
    paths: {
    	'sociallogin-photo': 'Bss_SocialLogin/js/photo',
        'sociallogin': 'Bss_SocialLogin/js/social-popup'
    },
    shim: {
        'sociallogin': {
            deps: ['jquery']
        },
        'sociallogin-photo': {
            deps: ['jquery']
        }
    }
};