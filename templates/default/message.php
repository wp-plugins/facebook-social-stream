<?php
/**
 * Do not change this default template!
 * 
 * 	You can create your own template in 
 * 	templates/{your-template-name}/message.php
 */

require_once(plugin_dir_path(__FILE__).'/../../lib/FBSS_TemplateStringUtils.php');

?>

	<!-- .fb-message -->
	<div class="fb-message" itemscope itemtype="http://schema.org/Article">
		<!-- .fb-info -->
		<div class="fb-info">
			<div class="fb-user-img">
				<img src="http://graph.facebook.com/<?php echo esc_html($page_name); ?>/picture" alt="<?php _e('profile picture', 'wp-fb-social-stream'); ?>" />
			</div>
			<div class="fb-metadata">
				<a href="<?php echo esc_html($msg_share_link); ?>" class="fb-share-link" rel="nofollow" target="_blank">
					<span class="fb-likes"><i class="fa fa-thumbs-o-up"></i> <?php echo esc_html($msg_likes); ?></span><br>
					<span class="fb-comments"><i class="fa fa-comment"></i> <?php echo esc_html($msg_comments); ?></span>
					
					<meta itemprop="interactionCount" content="UserLikes:<?php echo esc_html($msg_likes); ?>" />
					<meta itemprop="interactionCount" content="UserComments:<?php echo esc_html($msg_comments); ?>" />
				</a>
			</div>
		</div>
		<!-- /.fb-info -->

		<!-- .fb-message-wrap -->
		<div class="fb-message-wrap">
			<div class="fb-message-info">
				<span class="fb-message-date"><?php echo esc_html($msg_date_string); ?></span>
				<meta itemprop="datePublished" content="<?php echo esc_html($msg_date_iso_8601); ?>" />
				<?php if ($msg_share_link) : ?>
				<a href="<?php echo esc_html($msg_share_link); ?>" class="fb-share-link" rel="nofollow" target="_blank" itemprop="discussionUrl"><span class="fb-message-link"><i class="fa fa-link"></i> <?php _e('Link', 'wp-fb-social-stream'); ?></span></a>
				<?php endif; ?>
				<div class="clearer"></div>
			</div>
			
			<?php if ($msg_text) : ?>
			<div class="fb-message-text" itemprop="articleBody">
				<?php
					// escape external data first...
					$msg_text = esc_html($msg_text);
					
					// ...then create html output of JSON data
					echo FBSS_TemplateStringUtils::createMessageHTML($msg_text);
				?>
			</div>
			<?php endif; ?>

			<?php if ($msg_type == 'photo' && $img_src) : ?>
			<!-- .fb-message-image -->
			<div class="fb-message-image">
				<img src="<?php echo esc_html($img_src); ?>" alt="<?php _e('Message image', 'wp-fb-social-stream'); ?>" itemprop="image" />
			</div>
			<!-- /.fb-message-image -->
			<?php endif; ?>

			<?php if ($msg_type == 'link') : ?>
			<!-- .fb-message-linkbox -->
			<div class="fb-message-linkbox" onclick="window.open('<?php echo esc_html($link_src); ?>', '_blank'); return false;">
				<?php if ($link_img) : ?>
				<div class="fb-message-linkbox-img">
					<img src="<?php echo esc_html($link_img); ?>" alt="<?php _e('Link', 'wp-fb-social-stream'); ?> <?php echo esc_html($link_src); ?>" />
				</div>
				<?php endif; ?>
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
			
			<?php if ($msg_type == 'video' && $video_src) : ?>
			<!-- .fb-message-video -->
			<div class="fb-message-video">
				<?php if ($msg_status_type == 'added_video') : ?>
				<video controls>
  					<source src="<?php echo esc_html($video_src); ?>" type="video/mp4">
					<?php _e('Your browser does not support the video tag.', 'wp-fb-social-stream'); ?>
				</video>
				<div class="fb-message-video-metadata">
					<?php if ($video_name) : ?>
					<div class="fb-message-video-name"><?php echo esc_html($video_name); ?></div>
					<?php endif; ?>
					
					<?php if ($video_description) : ?>
					<div class="fb-message-video-desc">
						<?php 
							$video_description = esc_html($video_description);
							echo FBSS_TemplateStringUtils::createMessageHTML($video_description);
						?>
					</div>
					<?php endif; ?>
				</div>
				<?php else : ?>
				<div class="fb-message-video-linkbox">
					<div class="fb-message-video-img-wrapper" onclick="window.open('<?php echo esc_html($msg_share_link); ?>', '_blank'); return false;">
						<div class="fb-message-video-img">
							<img src="<?php echo esc_html($video_img); ?>" alt="" />
							<i class="fa fa-play-circle"></i>
						</div>
					</div>
					<div class="fb-message-video-txt">
						<?php if ($video_name) : ?>
						<div class="fb-message-video-name"><?php echo esc_html($video_name); ?></div>
						<?php endif; ?>
						
						<?php if ($video_description) : ?>
						<div class="fb-message-video-desc">
							<?php 
								$video_description = esc_html($video_description);
								echo FBSS_TemplateStringUtils::createMessageHTML($video_description);
							?>
						</div>
					</div>
					<div class="clearer"></div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<!-- /.fb-message-video -->
			<?php endif; ?>
		</div>
		<!-- /.fb-message-wrap -->
		
		<div class="clearer"></div>
	</div>
	<!-- /.fb-message -->
