require([
    'jquery',
	'mage/url'
    ], function ($, urlBuilder) {
        'use strict';

		$('.discount-coupon-form .primary').on('click', function() {
			if($(this).hasClass('apply')) {
			
				$('.coupon_error_message').html('');
				var couponText = $('.discount-coupon-form .field input[name="coupon_code"]').val();
				var remove = $('.discount-coupon-form input[name="remove"]').val();
				$.ajax({
					url: urlBuilder.build('checkout/cart/couponPost'),
					type: 'POST',
					dataType: 'json',
					data: {'form_key': $.mage.cookies.get('form_key'), 'remove': remove, 'coupon_code': couponText },
					showLoader: true,
					success: function(data) {
						if(data.status == 'success') {
							$('.coupon_error_message').html('<div class="message success">' + data.message + '</div>');$('.discount-coupon-form .actions-toolbar .primary.action ').removeClass('apply');
							$('.discount-coupon-form .actions-toolbar .primary.action').addClass('cancel');
							$('.discount-coupon-form .actions-toolbar .primary.action').val('Cancel Coupon');
							$('.discount-coupon-form .actions-toolbar .primary.action span').text('Cancel Coupon').css("font-size", "12px");
							$('.discount-coupon-form .field input[name="coupon_code"]').attr("disabled", true);
							
						}
						if(data.status == 'error') {
							$('.coupon_error_message').html('<div class="message error">' + data.message + '</div>');		
						}
						setTimeout(function(){ $('.coupon_error_message').html(''); }, 5000);						
					},
					error: function(data) {
						console.log('error');
						
					}
				});
			}
			
			if($(this).hasClass('cancel')) {
				var couponText = $('.discount-coupon-form .field input[name="coupon_code"]').val();
				$.ajax({
					url: urlBuilder.build('checkout/cart/couponPost'),
					type: 'POST',
					dataType: 'json',
					data: {'form_key': $.mage.cookies.get('form_key'), 'remove': 1, 'coupon_code': couponText },
					showLoader: true,
					success: function(data) {
						if(data.status == 'success') {
							$('.coupon_error_message').html('<div class="message success">' + data.message + '</div>');
							$('.discount-coupon-form .field input[name="coupon_code"]').removeAttr("disabled");
							$('.discount-coupon-form .field input[name="coupon_code"]').val('');
							$('.discount-coupon-form .actions-toolbar .primary.action').removeClass('cancel');
							$('.discount-coupon-form .actions-toolbar .primary.action').addClass('apply');
							$('.discount-coupon-form .actions-toolbar .primary.action').val('Apply Coupon');
							$('.discount-coupon-form .actions-toolbar .primary.action span').text('Apply Coupon');							
							
						}
						if(data.status == 'error') {
							$('.coupon_error_message').html('<div class="message error">' + data.message + '</div>');		
						}
						setTimeout(function(){ $('.coupon_error_message').html(''); }, 5000);						
					},
					error: function(data) {
						console.log('error');
						
					}
				});
			}
			
		});
		
});
