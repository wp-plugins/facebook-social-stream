			<!-- #wp-fb-social-stream-info-->
			<div id="wp-fb-social-stream-info">
				<div id="wp-fb-social-stream-last-update" class="wp-fb-social-stream-info-box">
					<h3><?php _e('Last stream update', 'wp-fb-social-stream'); ?> <a href="#" onclick="updateSocialStream(); return false;" title="<?php _e('update stream', 'wp-fb-social-stream'); ?>" id="wp-fb-social-stream-refresh"><i class="fa fa-refresh"></i></a></h3>
					<p><i class="fa fa-clock-o"></i> <span id="wp-fb-social-stream-last-update-time"><?php esc_html_e($view_data['stream_update_date']); ?></span></p>
				</div>

				<div id="wp-fb-social-stream-contact" class="wp-fb-social-stream-info-box">
					<h3><?php _e('Interact', 'wp-fb-social-stream'); ?></h3>
					<ul>
						<li><?php _e('Contact me for', 'wp-fb-social-stream'); ?>
							<ul>
								<li><a href="https://wordpress.org/support/plugin/facebook-social-stream" target="_blank"><i class="fa fa-ambulance"></i> <?php _e('support', 'wp-fb-social-stream'); ?></a></li>
								<li><a href="https://wordpress.org/support/plugin/facebook-social-stream" target="_blank"><i class="fa fa-rocket"></i> <?php _e('new features', 'wp-fb-social-stream'); ?></a></li>
							</ul>
							<?php _e('I\'ll do my best!', 'wp-fb-social-stream'); ?></li>
						<li class="wp-fb-social-stream-spacer"><?php _e('You like my work? Please', 'wp-fb-social-stream'); ?>
							<ul>
								<li><a href="https://wordpress.org/support/view/plugin-reviews/facebook-social-stream" target="_blank"><i class="fa fa-star"></i> <?php _e('vote for me', 'wp-fb-social-stream'); ?></a></li>
								<li><?php _e('and tell your friends', 'wp-fb-social-stream'); ?></li>
							</ul>
						</li>
					</ul>
				</div>

				<div id="wp-fb-social-stream-donate" class="wp-fb-social-stream-info-box">
					<h3><?php _e('Donation', 'wp-fb-social-stream'); ?></h3>
					<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WLXKFHGZ9WWGN" target="_blank"><i class="fa fa-coffee"></i> <?php _e('Coffee', 'wp-fb-social-stream'); ?></a> <?php _e('is very appreciated', 'wp-fb-social-stream'); ?> <i class="fa fa-smile-o"></i></p>
				</div>
			</div>
			<!-- /#wp-fb-social-stream-info-->