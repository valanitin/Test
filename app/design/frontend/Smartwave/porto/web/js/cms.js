require([
	'jquery',
	'domReady!'
], function ($) {
	$(document).ready(function () {
		var previousActiveTabIndex = 0;

		$(".tab-switcher").on('click keypress', function (event) {
			// event.which === 13 means the "Enter" key is pressed

			if ((event.type === "keypress" && event.which === 13) || event.type === "click") {

				var tabClicked = $(this).data("tab-index");

				if (tabClicked != previousActiveTabIndex) {
					$("#allTabsContainer .tab-container").each(function () {
						if ($(this).data("tab-index") == tabClicked) {
							$(".tab-container").hide();
							$(this).show();
							previousActiveTabIndex = $(this).data("tab-index");
							return;
						}
					});
				}
			}
		});
		var lastpreviousActiveTabIndex = 0;

		$(".last-tab-switcher").on('click keypress', function (event) {
			// event.which === 13 means the "Enter" key is pressed

			if ((event.type === "keypress" && event.which === 13) || event.type === "click") {

				var tabClicked = $(this).data("tab-index");

				if (tabClicked != lastpreviousActiveTabIndex) {
					$("#lastallTabsContainer .tab-container").each(function () {
						if ($(this).data("tab-index") == tabClicked) {
							$(".tab-container").hide();
							$(this).show();
							lastpreviousActiveTabIndex = $(this).data("tab-index");
							return;
						}
					});
				}
			}
		});

		$('.app-subscribe .sub-bttn').on('click', function () {
			var customerEmail = $('#newsletter').val();
			var formObj = {
				email: customerEmail
			};
			sendAppLink(formObj);
		});

		function sendAppLink(formObj) {

			$.ajax({
				type: 'GET',
				url: window.location.href + '/notify',
				data: formObj,
				showLoader: true,
				success: function (response) {
					alert(response);
				}
			});

		}


	});
});
