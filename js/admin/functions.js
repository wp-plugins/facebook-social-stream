var running = 0;

function updateSocialStream() {
	if (running) {
		return;
	}
	
	running = 1;

	jQuery('#wp-fb-social-stream-refresh i').removeClass('fa-refresh');
	jQuery('#wp-fb-social-stream-refresh i').addClass('fa-download');
	jQuery('#wp-fb-social-stream-refresh i').addClass('blink')
	
	jQuery.ajax({
		type:'POST',
		data:{action:'wp_fb_social_stream_force_update'},
		url:wp_fb_social_stream_js_vars.ajaxurl,
		success: function(data) {
			if (data) {
				jQuery("#wp-fb-social-stream-last-update-time").html(data);
				jQuery("#wp-fb-social-stream-last-update-time").css('background-color', 'yellow');
				jQuery('#wp-fb-social-stream-refresh i').removeClass('fa-download');
				jQuery('#wp-fb-social-stream-refresh i').removeClass('blink')
				jQuery('#wp-fb-social-stream-refresh i').addClass('fa-refresh');

				running = 0;
			}
		}
	});
}