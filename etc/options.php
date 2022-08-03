<?php

function iworks_pwa_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'use_tabs'        => true,
		'version'         => '0.0',
		'page_title'      => __( 'Progressive Web Application - Configuration', 'iworks-pwa' ),
		'menu_title'      => __( 'PWA', 'iworks-pwa' ),
		'menu'            => 'options',
		'enqueue_scripts' => array(),
		'enqueue_styles'  => array(),
		'options'         => array(
			array(
				'type'  => 'heading',
				'label' => __( 'General', 'iworks-pwa' ),
			),
			array(
				'name'              => 'app_name',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( 'Application Name', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
				'default'           => get_bloginfo( 'name' ),
			),
			array(
				'name'              => 'app_short_name',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( 'Application Short Name', 'iworks-pwa' ),
				'description'       => __( 'Used when there is insufficient space to display the full name of the application. 15 characters or less.', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
				'maxlength'         => 15,
				'default'           => get_bloginfo( 'name' ),
			),
			array(
				'name'              => 'app_description',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( 'Description', 'iworks-pwa' ),
				'description'       => __( 'A brief description of what your app is about.', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
				'default'           => get_bloginfo( 'description' ),
			),
			array(
				'name'        => 'app_orientation',
				'type'        => 'radio',
				'th'          => __( 'Orientation', 'iworks-pwa' ),
				'description' => __( 'Set the orientation of your app on devices. When set to &#8220;Follow Device Orientation&#8221; your app will rotate as the device is rotated.', 'iworks-pwa' ),
				'radio'       => array(
					'any'       => array(
						'label' => _x( 'Follow Device Orientation', 'PWA settings', 'iworks-pwa' ),
					),
					'portrait'  => array(
						'label' => _x( 'Portrait', 'PWA settings', 'iworks-pwa' ),
					),
					'landscape' => array(
						'label' => _x( 'Landscape', 'PWA settings', 'iworks-pwa' ),
					),
				),
				'default'     => 'portrait',
			),
			array(
				'name'        => 'app_display',
				'type'        => 'radio',
				'th'          => __( 'Display', 'iworks-pwa' ),
				'description' => __( 'Display mode decides what browser UI is shown when your app is launched. Standalone is default.', 'iworks-pwa' ),
				'radio'       => array(
					'fullscreen' => array(
						'label' => _x( 'Full Screen', 'PWA settings', 'iworks-pwa' ),
					),
					'standalone' => array(
						'label' => _x( 'Standalone', 'PWA settings', 'iworks-pwa' ),
					),
					'minimal-ui' => array(
						'label' => _x( 'Minimal UI', 'PWA settings', 'iworks-pwa' ),
					),
					'browser'    => array(
						'label' => _x( 'Browser', 'PWA settings', 'iworks-pwa' ),
					),
				),
				'default'     => 'standalone',
			),
			array(
				'name'              => 'cache_version',
				'type'              => 'number',
				'th'                => __( 'Cache version', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'default'           => 1,
				'class'             => 'small-text',
			),
			array(
				'name'    => 'offline_content',
				'type'    => 'textarea',
				'th'      => __( 'Offline content page', 'iworks-pwa' ),
				'default' => implode(
					array(
						__( 'We were unable to load the page you requested.', 'iworks-pwa' ),
						PHP_EOL,
						PHP_EOL,
						__( 'Please check your network connection and try again.', 'iworks-pwa' ),
					)
				),
				'classes' => array(
					'large-text',
					'code',
				),
				'rows'    => 10,
			),
			/**
			 * Section "Add to Home screen"
			 *
			 * @since 1.5.0
			 */
			array(
				'type'        => 'heading',
				'label'       => __( 'A2HS', 'iworks-pwa' ),
				'description' => __( 'Add to Home screen (or A2HS for short) is a feature available in modern browsers that allows a user to "install" a web app, ie. add a shortcut to their Home screen representing their favorite web app (or site) so they can subsequently access it with a single tap.', 'iworks-pwa' ),
				'since'       => '1.5.0',
			),
			array(
				'name'        => 'button_a2hs_position',
				'type'        => 'radio',
				'th'          => __( 'Position', 'iworks-pwa' ),
				'description' => __( '', 'iworks-pwa' ),
				'radio'       => array(
					'hide'         => array(
						'label'       => _x( 'Hide (recommended)', 'PWA settings', 'iworks-pwa' ),
						'description' => __( 'Browser will show A2HS prompt automatically if it is needed.', 'iworks-pwa' ),
					),
					'wp_body_open' => array(
						'label'       => _x( 'After &lt;body&gt; tag', 'PWA settings', 'iworks-pwa' ),
						'description' => __( 'This option doesn\'t work for "block themes".', 'iworks-pwa' ),
					),
					'wp_footer'    => array(
						'label' => _x( 'Footer', 'PWA settings', 'iworks-pwa' ),
					),
				),
				'default'     => 'hide',
				'since'       => '1.5.0',
			),
			array(
				'name'              => 'button_a2hs_text',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( 'Button text', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
				'default'           => __( 'Add to home screen', 'iworks-pwa' ),
				'since'             => '1.5.0',
			),
			array(
				'name'              => 'button_a2hs_css',
				'type'              => 'checkbox',
				'th'                => __( 'Load CSS', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since'             => '1.5.0',
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Generic', 'iworks-pwa' ),
			),
			array(
				'name'              => 'color_bg',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Background Color', 'iworks-pwa' ),
				'description'       => __( 'Background color of the splash screen.', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'color_theme',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Theme Color', 'iworks-pwa' ),
				'description'       => __( 'Theme color is used on supported devices to tint the UI elements of the browser and app switcher. When in doubt, use the same color as Background Color.', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'icon_app',
				'type'              => 'image',
				'th'                => __( 'Application Icon', 'iworks-pwa' ),
				'description'       => __( 'This will be the icon of your app when installed on the phone. It should be a PNG image.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
			),
			array(
				'name'              => 'icon_splash',
				'type'              => 'image',
				'type'              => 'special',
				'th'                => __( 'Splash Screen Icon', 'iworks-pwa' ),
				'description'       => __( 'This icon will be displayed on the splash screen of your app on supported devices. It should be a PNG image.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
			),
			/**
			 * Apple
			 */
			array(
				'type'  => 'heading',
				'label' => __( 'Apple', 'iworks-pwa' ),
			),
			array(
				'name'              => 'icon_apple',
				'type'              => 'image',
				'th'                => __( 'Touch Icon', 'iworks-pwa' ),
				'description'       => __( 'For ideal appearance on iOS when users add a progressive web app to the home screen. It must point to a non-transparent 192px (or 180px) square PNG.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
			),
			array(
				'name'              => 'splash_ipad_l',
				'type'              => 'image',
				'th'                => __( 'iPad landscape splash screen', 'iworks-pwa' ),
				'description'       => __( 'It must be a non-transparent PNG image. It should be exactly 1024x748px.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'sizes'             => '1024x748',
			),
			array(
				'name'              => 'splash_ipad_p',
				'type'              => 'image',
				'th'                => __( 'iPad portriat splash screen', 'iworks-pwa' ),
				'description'       => __( 'It must be a non-transparent PNG image. It should be exactly 768x1004px.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'sizes'             => '768x1004',
			),
			array(
				'name'              => 'splash_hr_p',
				'type'              => 'image',
				'th'                => __( 'iPhone, iPod high resolution portriat splash screen', 'iworks-pwa' ),
				'description'       => __( 'It must be a non-transparent PNG image. It should be exactly 640x960px.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'sizes'             => '640x960',
			),
			array(
				'name'              => 'splash_sr_p',
				'type'              => 'image',
				'th'                => __( 'iPhone, iPod high resolution portriat splash screen', 'iworks-pwa' ),
				'description'       => __( 'It must be a non-transparent PNG image. It should be exactly 320x480px.', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'sizes'             => '320x480',
			),
			array(
				'name'              => 'apple_pti',
				'type'              => 'image',
				'th'                => __( 'Pinned Tab Icon', 'iworks-pwa' ),
				'description'       => __( 'Use 100% black for all vectors with a transparent background in SVG format and add the following markup to all webpages that the icon should represent. The SVG file must be a single layer and the viewBox attribute must be set to "0 0 16 16".', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
			),
			array(
				'name'              => 'apple_ptic',
				'type'              => 'wpColorPicker',
				'th'                => __( 'Pinned Tab Icon Color', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'apple_sbc',
				'type'              => 'wpColorPicker',
				'th'                => __( 'Status Bar Color', 'iworks-pwa' ),
				'default'           => '#000',
				'sanitize_callback' => 'esc_html',
			),
			/**
			 * Microsoft
			 */
			array(
				'type'  => 'heading',
				'label' => __( 'Microsoft', 'iworks-pwa' ),
			),
			array(
				'name'              => 'ms_square',
				'type'              => 'image',
				'th'                => __( 'Square Tile Logo', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'description'       => __( 'It must be a PNG image, at least 310x310px.', 'iworks-pwa' ),
			),
			array(
				'name'              => 'ms_wide',
				'type'              => 'image',
				'th'                => __( 'Wide Tile Logo', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'description'       => __( 'It must be a PNG image. It should be exactly 310x150px.', 'iworks-pwa' ),
			),
		),
		'metaboxes'       => array(
			'assistance' => array(
				'title'    => __( 'We are waiting for your message', 'iworks-pwa' ),
				'callback' => 'iworks_pwa_options_need_assistance',
				'context'  => 'side',
				'priority' => 'core',
			),
			'love'       => array(
				'title'    => __( 'I love what I do!', 'iworks-pwa' ),
				'callback' => 'iworks_pwa_options_loved_this_plugin',
				'context'  => 'side',
				'priority' => 'core',
			),
		),
	);
	/**
	 * icons
	 */
	$options['icons'] = array(
		36   => array(
			'sizes'   => '36x36',
			'type'    => 'image/png',
			'density' => '0.75',
			'group'   => array(
				'manifest',
			),
		),
		48   => array(
			'sizes'   => '48x48',
			'type'    => 'image/png',
			'density' => '1.0',
			'group'   => array(
				'manifest',
			),
		),
		70   => array(
			'sizes'   => '70x70',
			'type'    => 'image/png',
			'density' => '1.5',
			'group'   => array(
				'ie11',
			),
		),
		72   => array(
			'sizes'   => '72x72',
			'type'    => 'image/png',
			'density' => '1.5',
			'group'   => array(
				'manifest',
			),
		),
		96   => array(
			'sizes'   => '96x96',
			'type'    => 'image/png',
			'density' => '2.0',
			'group'   => array(
				'manifest',
			),
		),
		144  => array(
			'sizes'   => '144x144',
			'type'    => 'image/png',
			'density' => '3.0',
			'group'   => array(
				'manifest',
				'windows8',
			),
		),
		150  => array(
			'sizes'   => '150x150',
			'type'    => 'image/png',
			'density' => '1.5',
			'group'   => array(
				'ie11',
			),
		),
		192  => array(
			'sizes'   => '192x192',
			'type'    => 'image/png',
			'density' => '4.0',
			'group'   => array(
				'manifest',
			),
		),
		310  => array(
			'sizes'   => '310x310',
			'type'    => 'image/png',
			'density' => '1.5',
			'group'   => array(
				'ie11',
			),
		),
		512  => array(
			'sizes' => '512x512',
			'type'  => 'image/png',
			'group' => array(
				'manifest',
			),
		),
		1024 => array(
			'sizes'   => '1024x1024',
			'type'    => 'image/png',
			'purpose' => 'any maskable',
			'group'   => array(
				'manifest',
			),
		),
	);
	/**
	 * Apple Touch Icons
	 */
	$options['apple_touch_icons'] = array(
		180 => array(
			'sizes'   => '180x180',
			'default' => true,
		),
		167 => array(
			'sizes' => '167x167',
		),
		152 => array(
			'sizes' => '152x152',
		),
		120 => array(
			'sizes' => '120x120',
		),
		114 => array(
			'sizes' => '114x114',
		),
		76  => array(
			'sizes' => '76x76',
		),
		72  => array(
			'sizes' => '72x72',
		),
		57  => array(
			'sizes' => '57x57',
		),
	);
	/**
	 * Microsoft Tile
	 */
	$options['ms_tile_square'] = array(
		310 => array(
			'sizes' => '310x310',
		),
		150 => array(
			'sizes' => '150x150',
		),
		70  => array(
			'sizes' => '70x70',
		),
	);
	/**
	 * service worker handler
	 */
	$options['service-worker-handler'] = 'iworks-pwa-service-worker-js';
	/**
	 * cache name
	 */
	$options['cache-name'] = 'iworks-pwa-offline-cache';
	/**
	 * offline page
	 */
	$options['offline-page'] = 'iworks-pwa-offline';
	/**
	 * manifest.json
	 */
	$options['manifest.json'] = 'manifest.json';
	/**
	 * return
	 */
	return apply_filters( 'iworks_plugin_get_options', $options, 'iworks-pwa' );
}

function iworks_pwa_options_loved_this_plugin( $iworks_iworks_seo_improvements ) {
	$content = apply_filters( 'iworks_rate_love', '', 'iworks-pwa' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}
	?>
<p><?php _e( 'Below are some links to help spread this plugin to other users', 'iworks-pwa' ); ?></p>
<ul>
	<li><a href="https://wordpress.org/support/plugin/iworks-pwa/reviews/#new-post"><?php _e( 'Give it a five stars on WordPress.org', 'iworks-pwa' ); ?></a></li>
	<li><a href="<?php _ex( 'https://wordpress.org/plugins/iworks-pwa/', 'plugin home page on WordPress.org', 'iworks-pwa' ); ?>"><?php _e( 'Link to it so others can easily find it', 'iworks-pwa' ); ?></a></li>
</ul>
	<?php
}
function iworks_pwa_taxonomies() {
	$data       = array();
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
	foreach ( $taxonomies as $taxonomy ) {
		$data[ $taxonomy->name ] = $taxonomy->labels->name;
	}
	return $data;
}
function iworks_pwa_post_types() {
	$args       = array(
		'public' => true,
	);
	$p          = array();
	$post_types = get_post_types( $args, 'names' );
	foreach ( $post_types as $post_type ) {
		$a               = get_post_type_object( $post_type );
		$p[ $post_type ] = $a->labels->name;
	}
	return $p;
}

function iworks_pwa_options_need_assistance( $iworks_iworks_seo_improvementss ) {
	$content = apply_filters( 'iworks_rate_assistance', '', 'iworks-pwa' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}

	?>
<p><?php _e( 'We are waiting for your message', 'iworks-pwa' ); ?></p>
<ul>
	<li><a href="<?php _ex( 'https://wordpress.org/support/plugin/iworks-pwa/', 'link to support forum on WordPress.org', 'iworks-pwa' ); ?>"><?php _e( 'WordPress Help Forum', 'iworks-pwa' ); ?></a></li>
</ul>
	<?php
}
