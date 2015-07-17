jQuery(document).ready(function() {
	if ($(".wp-fb-social-stream").length) {
		jQuery.ajax({
			type:'POST',
			data:{action:'wp_fb_social_stream_update'},
			url:'/wp-admin/admin-ajax.php',
			success: function(data) {
				if (data) {
					$(".wp-fb-social-stream").html(data);
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