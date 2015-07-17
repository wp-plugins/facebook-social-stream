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
				<a href="<?php echo esc_html($msg_share_link); ?>" class="fb-share-link" rel="nofollow" target="_blank">
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
				<?php if ($msg_share_link) : ?>
				<a href="<?php echo esc_html($msg_share_link); ?>" class="fb-share-link" rel="nofollow" target="_blank"><span class="fb-message-link"><i class="fa fa-link"></i> Link</span></a>
				<?php endif; ?>
				<div class="clearer"></div>
			</div>
			
			<?php if ($msg_text) : ?>
			<div class="fb-message-text"><?php echo esc_html($msg_text); ?></div>
			<?php endif; ?>

			<?php if ($msg_type == 'photo' && $img_src) : ?>
			<!-- .fb-message-image -->
			<div class="fb-message-image">
				<img src="<?php echo esc_html($img_src); ?>" alt="Nachrichtenbild <?php echo esc_html($i); ?>" />
			</div>
			<!-- /.fb-message-image -->
			<?php endif; ?>

			<?php if ($msg_type == 'link') : ?>
			<!-- .fb-message-linkbox -->
			<div class="fb-message-linkbox" onclick="window.open('<?php echo esc_html($link_src); ?>', '_blank'); return false;">
				<div class="fb-message-linkbox-img">
				<?php if ($link_img) : ?>
					<img src="<?php echo esc_html($link_img); ?>" />
				<?php endif; ?>
				</div>
				<div class="fb-message-linkbox-txt">
				<?php if ($link_name) : ?>
					<div class="fb-message-linkbox-name">
						<?php echo esc_html($link_name); ?>
					</div>
				<?php endif; ?>
				<?php if ($link_description) : ?>
					<div class="fb-message-linkbox-desc">
						<?php echo esc_html($link_description); ?>
					</div>
				<?php endif; ?>
				<?php if ($link_caption) : ?>
					<div class="fb-message-linkbox-caption">
						<?php echo esc_html($link_caption); ?>
					</div>
				<?php endif; ?>
				</div>
				
				<div class="clearer"></div>
			</div>
			<!-- /.fb-message-linkbox -->
			<?php endif; ?>

		</div>
		<!-- /.fb-message-wrap -->
		
		<div class="clearer"></div>
	</div>
	<!-- /.fb-message -->
