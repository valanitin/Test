require([
'jquery',
'Magento_Checkout/js/model/quote',
'Magento_Checkout/js/model/shipping-service',
'Magento_Checkout/js/model/shipping-rate-registry',
'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
'Magento_Checkout/js/model/shipping-rate-processor/new-address',
], function($, quote, shippingService, rateRegistry, 
  customerAddressProcessor, newAddressProcessor) {
    
    // $(document).ready(function() {
    //     console.log('test');
    // });

    $('select[name="country_id"], input[name="telephone"]').live('change', function(e) {
        
        // console.log('changed country');
        var address = quote.shippingAddress();
        rateRegistry.set(address.getCacheKey(), null);
        newAddressProcessor.getRates(address);
    
    });

    $('select[name="country_id"]').focusout(function(e) {
        
        var address = quote.shippingAddress();
        rateRegistry.set(address.getCacheKey(), null);
        newAddressProcessor.getRates(address);
    
    });

});