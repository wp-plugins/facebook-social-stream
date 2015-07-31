					<h3><?php _e('Facebook options', 'wp-fb-social-stream'); ?></h3>
					<table class="form-table wp-fb-social-stream-customization-fb-options">
						<tbody>
				        	<tr>
				        		<th scope="row">
				        			<label for="fbss_setting_fb_page_name"><?php _e('Facebook Page Name', 'wp-fb-social-stream'); ?></label>
				        		</th>
				        		<td>
				        			<input type="text" name="fbss_setting_fb_page_name" value="<?php esc_attr_e( get_option('fbss_setting_fb_page_name') ); ?>" class="regular-text" />
				        			<p class="description"><?php _e('You can idintify the page-name as follows: https://www.facebook.com/{<strong>page-name</strong>}', 'wp-fb-social-stream'); ?></p>
				        		</td>
				        	</tr>
							<tr>
								<th scope="row">
									<label for="fbss_setting_fb_access_token"><?php _e('Facebook Access Token', 'wp-fb-social-stream'); ?></label>
								</th>
						        <td>
						        	<input type="text" name="fbss_setting_fb_access_token" value="<?php esc_attr_e( get_option('fbss_setting_fb_access_token') ); ?>" class="regular-text" /> (<?php _e('optional', 'wp-fb-social-stream'); ?>)
						        	<p class="description"><?php _e('Either use your own one with specific rights or leave it empty to use the plugin-default', 'wp-fb-social-stream'); ?></p>
					        	</td>
							</tr>
						<tbody>
					</table>