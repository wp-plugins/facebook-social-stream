<?php
/**
 * Do not change this default template!
 * 
 * 	You can create your own template in 
 * 	templates/{your-template-name}/message.php
 */
?>

	<!-- .fb-message -->
	<div class="fb-message">
		<!-- .fb-info -->
		<div class="fb-info">
			<div class="fb-user-img">
				<img src="http://graph.facebook.com/<?php echo esc_html($page_name); ?>/picture" alt="Profilbild" />
			</div>
			<div class="fb-metadata">
				<a href="<?php echo esc_html($msg_link); ?>" rel="nofollow" target="_blank">
					<span class="fb-likes"><i class="fa fa-thumbs-o-up"></i> <?php echo esc_html($msg_likes); ?></span> <br>
					<span class="fb-comments"><i class="fa fa-comment"></i> <?php echo esc_html($msg_comments); ?></span>
				</a>
			</div>
		</div>
		<!-- /.fb-info -->

		<!-- .fb-message-wrap -->
		<div class="fb-message-wrap">
			<div class="fb-message-info">
				<span class="fb-message-date">am <?php echo esc_html($msg_date_day); ?>. <?php echo esc_html($msg_date_month); ?></span>
				<a href="<?php echo esc_html($msg_link); ?>" rel="nofollow" target="_blank"><span class="fb-message-link"><i class="fa fa-link"></i> Link</span></a>
				<div class="clearer"></div>
			</div>
			<div class="fb-message-text"><?php echo esc_html($msg_text); ?></div>

<?php if ($img_src) : ?>
			<div class="fb-message-image">
				<img src="<?php echo esc_html($img_src); ?>" alt="Nachrichtenbild <?php echo esc_html($i); ?>" />
			</div>
<?php endif; ?>
			
		</div>
		<!-- /.fb-message-wrap -->
		
		<div class="clearer"></div>
	</div>
	<!-- /.fb-message -->
