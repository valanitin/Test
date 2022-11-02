var config = {
    map: {
        '*': {
            'AjaxCart': 'RLTSquare_AjaxShoppingCartUpdate/js/cartValueIncDec',
            'CartQtyUpdate': 'RLTSquare_AjaxShoppingCartUpdate/js/cartQtyUpdate'
        }
    },
    shim: {
        AjaxCart: {
            deps: ['jquery']
        },
        CartQtyUpdate: {
            deps: ['jquery']
        }
    }
};