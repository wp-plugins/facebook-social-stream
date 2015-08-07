			<!-- #fbss-extensions -->
			<div id="fbss-extensions">
			
				<?php if ( count($view_data['extensions_installed']) ) : ?>
				<h3><?php _e('Installed extensions', 'wp-fb-social-stream'); ?></h3>
				
				<!-- .fbss-extension-installed-list -->
				<div class="fbss-extension-installed-list">
					
					<?php foreach ($view_data['extensions_installed'] as $ext_key => $ext_val) : ?>
					<!-- .fbss-extension-installed-item -->
					<div class="fbss-extension-installed-item">
						
						<!-- .fbss-extension-installed-data -->
						<div class="fbss-extension-data">
							<!-- .fbss-extension-hl -->
							<div class="fbss-extension-hl">
								<span class="fbss-extension-name"><?php esc_html_e( $view_data['extensions_installed'][$ext_key]['name'] ); ?></span>
								<span class="fbss-extension-v">(<?php esc_html_e( $view_data['extensions_installed'][$ext_key]['version'] ); ?>)</span>
								<span class="fbss-extension-license-status">
									<?php if($view_data['extensions_installed'][$ext_key]['valid']) : ?>
									<span class="fbss-extension-license-active"><?php _e('active', 'wp-fb-social-stream'); ?></span>
										<?php if ($view_data['extensions_installed'][$ext_key]['license_response']->license == 'expired') : ?>
										<span class="fbss-extension-license-inactive">(<?php printf( __('support expired %s', 'wp-fb-social-stream'), $view_data['extensions_installed'][$ext_key]['license_response']->expires ); ?>)</span>
										<?php endif; ?>
									<?php else : ?>
									<span class="fbss-extension-license-inactive"><?php _e('inactive', 'wp-fb-social-stream'); ?></span>
									<?php endif; ?>
								</span>
							</div>
							<!-- /.fbss-extension-hl -->
							
							<!-- .fbss-extension-desc -->
							<div class="fbss-extension-desc">
								<?php esc_html_e( $view_data['extensions_installed'][$ext_key]['desc'] ); ?>
							</div>
							<!-- /.fbss-extension-desc -->
						</div>
						<!-- /.fbss-extension-installed-data -->
						
						<!-- .fbss-extension-license -->
						<div class="fbss-extension-license">
							<input name="fbss_extension_license_key_<?php esc_attr_e($view_data['extensions_installed'][$ext_key]['id']) ?>" value="<?php esc_attr_e( get_option('fbss_extension_license_key_'.esc_attr($view_data['extensions_installed'][$ext_key]['id'])) ); ?>" type="text" placeholder="<?php _e('license key', 'wp-fb-social-stream'); ?>" class="regular-text" />
							
							<?php if($view_data['extensions_installed'][$ext_key]['valid']) : ?>
							<input type="submit" class="button button-primary" value="<?php _e('update', 'wp-fb-social-stream'); ?>" />
							<?php else : ?>
							<input type="submit" class="button button-primary" value="<?php _e('activate', 'wp-fb-social-stream'); ?>" />
							<?php endif; ?>
						</div>
						<!-- /.fbss-extension-license -->
						
					</div>
					<!-- /.fbss-extension-installed-item -->
					<?php endforeach; ?>

				</div>
				<!-- /.fbss-extension-installed-list -->
				<?php endif; ?>
					
				<h3><?php _e('Available extensions', 'wp-fb-social-stream'); ?></h3>
					
				<?php if ( count($view_data['extensions_available']) ) : ?>
				<!-- .fbss-extension-available-list -->
				<div class="fbss-extension-available-list">
				
				  	<?php foreach ($view_data['extensions_available'] as $ext_key => $ext_val) : ?>
				  	<!-- .fbss-extension-available-item -->
				  	<div class="fbss-extension-available-item">
				  		<!-- .fbss-extension-img -->
				  		<div class="fbss-extension-img">
				  			<a href="<?php esc_attr_e( $view_data['extensions_available'][$ext_key]['download'] ); ?>" target="_blank" title="<?php esc_attr_e( $view_data['extensions_available'][$ext_key]['name'] ); ?>">
				  				<img src="<?php esc_attr_e( $view_data['extensions_available'][$ext_key]['img'] ); ?>" alt="<?php esc_attr_e( $view_data['extensions_available'][$ext_key]['name'] ); ?>" />
				  			</a>
				  		</div>
				  		<!-- /.fbss-extension-img -->
				  		
				  		<!-- .fbss-extension-data -->
				  		<div class="fbss-extension-data">
			  				<div class="fbss-extension-name"><?php esc_html_e( $view_data['extensions_available'][$ext_key]['name'] ); ?></div>
				  			<div class="fbss-extension-desc"><?php esc_html_e( $view_data['extensions_available'][$ext_key]['desc'] ); ?></div>
				  			<div class="fbss-extension-dl">
				  				<a href="<?php esc_attr_e( $view_data['extensions_available'][$ext_key]['download'] ); ?>" target="_blank" title="<?php _e('Download', 'wp-fb-social-stream'); ?>" class="button button-primary"><?php _e('Download', 'wp-fb-social-stream'); ?></a>
				  			</div>
				  		</div>
				  		<!-- /.fbss-extension-data -->
				  	</div>
				  	<!-- /.fbss-extension-available-item -->
				 	<?php endforeach; ?>
				
					<div class="clearer"></div>
				</div>
				<!-- /.fbss-extension-available-list -->
				
				<?php else : ?>
				<p><?php _e('Congratulations! You have installed all available extensions.', 'wp-fb-social-stream'); ?></p>
				<?php endif; ?>
				
			</div>
			<!-- /#fbss-extensions -->
