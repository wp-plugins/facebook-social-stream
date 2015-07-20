<?php
/**
 * Do not change this default template-configuration!
 * 
 * 	You can create your own template in 
 * 	templates/{your-template-name}/config.php
 */

require_once(plugin_dir_path(__FILE__).'../../lib/FBSS_Registry.php');

$template_config = array(
	'api_version'	=> '1.0.0',
	'css'			=> array(
		array(
			'name'		=> __('Message-box', 'wp-fb-social-stream'),
			'desc'		=> __('The main message box', 'wp-fb-social-stream'),
			'config'	=> array(
				'index'		=> 'message_box',
				'configs'	=> array(
					array(
						'config_id'		=> 1,
						'desc'			=> __('Background color', 'wp-fb-social-stream'),
						'selector' 		=> '.wp-fb-social-stream .fb-message-wrap',
						'property'		=> 'background-color',
						'type'			=> 'hexcode'
					),
					array(
						'config_id'		=> 2,
						'desc'			=> __('Border color', 'wp-fb-social-stream'),
						'selector' 		=> '.wp-fb-social-stream .fb-message-wrap',
						'property'		=> 'border-color',
						'type'			=> 'hexcode'
					),
					array(
						'config_id'		=> 3,
						'desc'			=> '',
						'selector' 		=> '.wp-fb-social-stream .fb-message-wrap:after',
						'property'		=> 'border-color',
						'type'			=> 'hexcode',
						'actions'		=> array(
							'hide'				=> true,
							'copy_value_from'	=> array( // e.g. border-color: transparent #EDEDED;
								'index'			=> 'message_box',
								'config_id'		=> 2,
								'value_prefix'	=> 'transparent ',
								'value_suffix'	=> ''
							)
						)
					)
				)
			)
		),
		array(
			'name'		=> __('Meta-box', 'wp-fb-social-stream'),
			'desc'		=> __('The meta-box contains the like and comment count', 'wp-fb-social-stream'),
			'config'	=> array(
				'index'			=> 'meta_box',
				'configs'		=> array(
					array(
						'config_id'		=> 1,
						'desc'			=> __('Background color', 'wp-fb-social-stream'),
						'selector' 		=> '.wp-fb-social-stream .fb-metadata',
						'property'		=> 'background-color',
						'type'			=> 'hexcode'
					)
				)
			)
		),
		array(
			'name'		=> __('Link-box', 'wp-fb-social-stream'),
			'desc'		=> __('Links inside the message-box are wrapped by the link-box', 'wp-fb-social-stream'),
			'config'	=> array(
				'index'		=> 'link_box',
				'configs'	=> array(
					array(
						'config_id'		=> 1,
						'desc'			=> __('Background color', 'wp-fb-social-stream'),
						'selector' 		=> '.wp-fb-social-stream .fb-message-linkbox',
						'property'		=> 'background-color',
						'type'			=> 'hexcode'
					)
				)
			)
		)
	)
);

// register configuration
FBSS_Registry::set('template_config', $template_config);

