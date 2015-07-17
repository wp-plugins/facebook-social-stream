jQuery(document).ready(function() {
	if (jQuery(".wp-fb-social-stream").length) {
		jQuery.ajax({
			type:'POST',
			data:{action:'wp_fb_social_stream_update'},
			url:wp_fb_social_stream_js_vars.ajaxurl,
			success: function(data) {
				if (data) {
					jQuery(".wp-fb-social-stream").html(data);
				}
			},
			error: function(xhr, status) {
				// alert("Sorry, could not update social stream!");
			},
			complete: function(xhr, status) {
				// nothing to do here
			}
		});		
	}
});