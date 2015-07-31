		<?php settings_errors(); ?>

		<!-- .wrap -->
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo get_admin_url(); ?>admin.php?page=<?php echo $view_data['menu_slug']; ?>&tab=settings" id="nav-tab-settings" class="nav-tab<?php echo ($view_data['active_tab'] == 'settings') ? ' nav-tab-active' : ''; ?>"><?php _e('Settings', 'wp-fb-social-stream'); ?></a>
				<a href="<?php echo get_admin_url(); ?>admin.php?page=<?php echo $view_data['menu_slug']; ?>&tab=styling" id="nav-tab-styling" class="nav-tab<?php echo ($view_data['active_tab'] == 'styling') ? ' nav-tab-active' : ''; ?>"><?php _e('Styling', 'wp-fb-social-stream'); ?></a>
				<a href="<?php echo get_admin_url(); ?>admin.php?page=<?php echo $view_data['menu_slug']; ?>&tab=extensions" id="nav-tab-extensions" class="nav-tab<?php echo ($view_data['active_tab'] == 'extensions') ? ' nav-tab-active' : ''; ?>"><?php _e('Extensions', 'wp-fb-social-stream'); ?></a>
			</h2>

			<!-- #wp-fb-social-stream-settings -->
			<div id="wp-fb-social-stream-settings">
				<form method="post" action="options.php" id="wp-fb-social-stream-settings-form">

				<?php
					if ($view_data['active_tab'] == 'settings') {
						settings_fields('wp-fb-social-stream-settings-group');
						do_settings_sections('wp-fb-social-stream-settings-group');
					} elseif ($view_data['active_tab'] == 'styling') {
						settings_fields('wp-fb-social-stream-styling-group');
						do_settings_sections('wp-fb-social-stream-styling-group');
					} elseif ($view_data['active_tab'] == 'extensions') {
						settings_fields('wp-fb-social-stream-extensions-group');
						do_settings_sections('wp-fb-social-stream-extensions-group');
					}

					if ( isset($view_data['fb_options']) ) {
						echo $view_data['fb_options'];
						submit_button();
					}
					
					if ( isset($view_data['stream_options']) ) {
						echo '<hr />';
						echo $view_data['stream_options'];
						submit_button();
					}
					
					if ( isset($view_data['styling_options']) ) {
						echo $view_data['styling_options'];
						submit_button();
					}
					
					if ( isset($view_data['extensions']) ) {
						echo $view_data['extensions'];
					}
				?>
				</form>
			</div>
			<!-- /#wp-fb-social-stream-settings -->
			
			<?php echo $view_data['stream_info_col']; ?>
		</div>
		<!-- .wrap -->