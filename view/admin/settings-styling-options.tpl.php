				<h3><?php _e('Stream customization', 'wp-fb-social-stream'); ?></h3>
				<p><?php _e('Optional style configuration of your social stream', 'wp-fb-social-stream'); ?></p>
				<table class="form-table wp-fb-social-stream-customization">
					<tbody>
					<?php foreach ($view_data['table_rows'] as $row) : ?>
 	        			<tr>
 							<th scope="row" colspan="2">
 								<?php esc_html_e( __($row['name'], 'wp-fb-social-stream') ); ?>
 								<p class="description"><?php esc_html_e( __($row['desc'], 'wp-fb-social-stream') ); ?></p>
 							</th>
 						</tr>
 						<?php foreach ($row['child_rows'] as $child_row) : ?>
	        			<tr>
							<td>
								<?php esc_html_e( __($child_row['desc'], 'wp-fb-social-stream') ); ?>
								<p class="description"><?php esc_html_e($child_row['selector']); ?></p>
							</td>
							<td>
							<?php if ($child_row['type'] == 'hexcode') :?>
								#<input type="text" name="<?php esc_attr_e($child_row['input_name']); ?>" value="<?php esc_attr_e($child_row['input_val']); ?>" class="hexcode" size="6" maxlength="6" style="<?php echo ($child_row['input_val']) ? 'border: 2px solid #'.esc_attr($child_row['input_val']) : '' ?>" />
							<?php else : ?>
								<input type="text" name="<?php esc_attr_e($child_row['input_name']); ?>" value="<?php esc_attr_e($child_row['input_val']); ?>" class="regular-text" />
							<?php endif; ?>
							</td>
						</tr>
 						<?php endforeach; ?>
					<?php endforeach; ?>
					<tbody>
				</table>