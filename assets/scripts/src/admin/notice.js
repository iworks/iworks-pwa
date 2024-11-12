jQuery( document ).ready(function($) {
	window.setTimeout( function() {
		$('.iworks-pwa-notice-check-url .notice-dismiss').on( 'click', function() {
			var $parent = $(this).closest('.iworks-pwa-notice-check-url');
			var data = {
				'action' : $parent.data('action'),
				'nonce' : $parent.data('nonce'),
			};
			$.post(window.ajaxurl, data);
		});
	}, 1000
	);
});
