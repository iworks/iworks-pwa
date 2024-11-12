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
				'default'           => substr( get_bloginfo( 'name' ), 0, 45 ),
			),
			array(
				'name'              => 'app_short_name',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( 'Application Short Name', 'iworks-pwa' ),
				'description'       => __( 'Used when there is insufficient space to display the full name of the application. 15 characters or less.', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
				'maxlength'         => 15,
				'default'           => substr( get_bloginfo( 'name' ), 0, 15 ),
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
				'name'        => 'app_scope',
				'type'        => 'radio',
				'th'          => __( 'Scope', 'iworks-pwa' ),
				'description' => __( 'The scope defines the navigation scope of this web application\'s application context. It restricts what web pages can be viewed while the manifest is applied. If the user navigates outside the scope, it reverts to a normal web page inside a browser tab or window.', 'iworks-pwa' ),
				'radio'       => array(
					'relative'     => array(
						'label'       => _x( 'Relative to the base URL.', 'PWA settings', 'iworks-pwa' ),
						'description' => _x( 'If the scope is relative, the manifest URL is used as a base URL.', 'PWA settings', 'iworks-pwa' ),
					),
					'current-site' => array(
						'label'       => _x( 'Current site', 'PWA settings', 'iworks-pwa' ),
						'description' => _x( 'If the scope is limited to current site the following scope limits navigation to the current site.', 'PWA settings', 'iworks-pwa' ),
					),
				),
				'default'     => 'relative',
				'since'       => '1.6.2',
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
			 * Section "Categories"
			 *
			 * @since 1.6.3
			 */
			array(
				'type'        => 'heading',
				'label'       => __( 'Categories', 'iworks-pwa' ),
				'description' => esc_html__(
					'The categories member is an array of strings defining the names of categories that the application supposedly belongs to.',
					'iworks-pwa'
				),
			),
			array(
				'name'    => 'categories',
				'type'    => 'checkbox_group',
				'options' => array(
					'books'           => esc_html__( 'Books', 'iworks-pwa' ),
					'business'        => esc_html__( 'Business', 'iworks-pwa' ),
					'education'       => esc_html__( 'Education', 'iworks-pwa' ),
					'entertainment'   => esc_html__( 'Entertainment', 'iworks-pwa' ),
					'finance'         => esc_html__( 'Finance', 'iworks-pwa' ),
					'fitness'         => esc_html__( 'Fitness', 'iworks-pwa' ),
					'food'            => esc_html__( 'Food', 'iworks-pwa' ),
					'games'           => esc_html__( 'Games', 'iworks-pwa' ),
					'government'      => esc_html__( 'Government', 'iworks-pwa' ),
					'health'          => esc_html__( 'Health', 'iworks-pwa' ),
					'kids'            => esc_html__( 'Kids', 'iworks-pwa' ),
					'lifestyle'       => esc_html__( 'Lifestyle', 'iworks-pwa' ),
					'magazines'       => esc_html__( 'Magazines', 'iworks-pwa' ),
					'medical'         => esc_html__( 'Medical', 'iworks-pwa' ),
					'music'           => esc_html__( 'Music', 'iworks-pwa' ),
					'navigation'      => esc_html__( 'Navigation', 'iworks-pwa' ),
					'news'            => esc_html__( 'News', 'iworks-pwa' ),
					'personalization' => esc_html__( 'Personalization', 'iworks-pwa' ),
					'photo'           => esc_html__( 'Photo', 'iworks-pwa' ),
					'politics'        => esc_html__( 'Politics', 'iworks-pwa' ),
					'productivity'    => esc_html__( 'Productivity', 'iworks-pwa' ),
					'security'        => esc_html__( 'Security', 'iworks-pwa' ),
					'shopping'        => esc_html__( 'Shopping', 'iworks-pwa' ),
					'social'          => esc_html__( 'Social', 'iworks-pwa' ),
					'sports'          => esc_html__( 'Sports', 'iworks-pwa' ),
					'travel'          => esc_html__( 'Travel', 'iworks-pwa' ),
					'utilities'       => esc_html__( 'Utilities', 'iworks-pwa' ),
					'weather'         => esc_html__( 'Weather', 'iworks-pwa' ),
				),
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
				'th'                => __( 'Load CSS', 'iworks-pwa' ),
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
				'name'              => 'icon_maskable',
				'type'              => 'image',
				'th'                => __( 'Maskable Icon', 'iworks-pwa' ),
				'description'       => __( 'Maskable icon is adaptive icon that can be displayed in a variety of shapes that operating systems provide. For example, on Android, app icons can have a circular mask. Your PWA app icon should specifically support masking to look well integrated with operating systems that apply masks.', 'iworks-pwa' ),
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
			/**
			 * Pinned Tab Icon
			 */
			array(
				'type'  => 'subheading',
				'label' => __( 'Pinned Tab Icon', 'iworks-pwa' ),
			),
			'apple_pti' => array(
				'name'              => 'apple_pti',
				'type'              => 'image',
				'th'                => __( 'Icon', 'iworks-pwa' ),
				'description'       => __( 'Use 100% black for all vectors with a transparent background in SVG format and add the following markup to all webpages that the icon should represent. The SVG file must be a single layer and the viewBox attribute must be set to "0 0 16 16".', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
			),
			array(
				'name'              => 'apple_ptic',
				'type'              => 'wpColorPicker',
				'th'                => __( 'Color', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
			),
			/**
			 * Status Bar
			 */
			array(
				'type'  => 'subheading',
				'label' => __( 'Status Bar', 'iworks-pwa' ),
			),
			array(
				'name'        => 'apple_status_bar_style',
				'type'        => 'radio',
				'th'          => __( 'Style', 'iworks-pwa' ),
				'description' => __( 'Customize the iOS status bar (the area along the upper edge of the screen displaying the time and battery status) of your PWA.', 'iworks-pwa' ),
				'radio'       => array(
					'default'           => array(
						'label' => _x( 'White status bar with black text and symbols.', 'PWA settings', 'iworks-pwa' ),
					),
					'black'             => array(
						'label' => _x( 'Black status bar and black text and symbols, making it appear completely black.', 'PWA settings', 'iworks-pwa' ),
					),
					'black-translucent' => array(
						'label' => _x( 'White text and symbols, and the status bar will take the same background color as the body element of your web app.', 'PWA settings', 'iworks-pwa' ),
					),
				),
				'default'     => 'default',
			),
			/**
			 * Custom Splash Screen
			 */
			array(
				'type'        => 'subheading',
				'label'       => __( 'Custom Splash Screen', 'iworks-pwa' ),
				'description' => implode(
					PHP_EOL . PHP_EOL,
					array(
						__( 'Add the following elements to support custom splash screens for the different iOS devices.', 'iworks-pwa' ),
						__( 'It must be a non-transparent PNG image.', 'iworks-pwa' ),
					)
				),
			),
			/**
			 * @since 1.5.4
			 *
			 * default
			 */
			array(
				'name'              => 'splash_image',
				'type'              => 'image',
				'th'                => __( 'Default', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 0, 0, 0 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone Xs Max (1242px x 2688px)
			 */
			array(
				'name'              => 'splash_iphone_xs_max',
				'type'              => 'image',
				'th'                => __( 'iPhone Xs Max', 'iworks-pwa' ),
				'description'       => __( '1242px &#10005; 2688px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 414, 896, 3 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone Xr (828px x 1792px)
			 */
			array(
				'name'              => 'splash_iphone_xr',
				'type'              => 'image',
				'th'                => __( 'iPhone Xr', 'iworks-pwa' ),
				'description'       => __( '828px &#10005; 1792px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 414, 896, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone X, Xs (1125px x 2436px)
			 */
			array(
				'name'              => 'splash_iphone_x_xs',
				'type'              => 'image',
				'th'                => __( 'iPhone X, Xs', 'iworks-pwa' ),
				'description'       => __( '1125px &#10005; 2436px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 375, 812, 3 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus (1242px x 2208px)
			 */
			array(
				'name'              => 'splash_iphone_8p_7p_6sp_6p',
				'type'              => 'image',
				'th'                => __( 'iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus', 'iworks-pwa' ),
				'description'       => __( '1242px &#10005; 2208px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 414, 736, 3 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone 8, 7, 6s, 6 (750px x 1334px)
			 */
			array(
				'name'              => 'splash_iphone_8_7_6s_6',
				'type'              => 'image',
				'th'                => __( 'iPhone 8, 7, 6s, 6', 'iworks-pwa' ),
				'description'       => __( '750px &#10005; 1334px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 375, 667, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPad Pro 12.9" (2048px x 2732px)
			 */
			array(
				'name'              => 'splash_ipad_pro_12_9',
				'type'              => 'image',
				'th'                => __( 'iPad Pro 12.9"', 'iworks-pwa' ),
				'description'       => __( '2048px &#10005; 2732px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 1024, 1366, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPad Pro 11” (1668px x 2388px)
			 */
			array(
				'name'              => 'splash_ipad_pro_11',
				'type'              => 'image',
				'th'                => __( 'iPad Pro 11”', 'iworks-pwa' ),
				'description'       => __( '1668px &#10005; 2388px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 834, 1194, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPad Pro 10.5" (1668px x 2224px)
			 */
			array(
				'name'              => 'splash_ipad_pro_10_5',
				'type'              => 'image',
				'th'                => __( 'iPad Pro 10.5"', 'iworks-pwa' ),
				'description'       => __( '1668px &#10005; 2224px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 834, 1112, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPad Mini, Air (1536px x 2048px)
			 */
			array(
				'name'              => 'splash_ipad_mini_air',
				'type'              => 'image',
				'th'                => __( 'iPad Mini, Air', 'iworks-pwa' ),
				'description'       => __( '1536px &#10005; 2048px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 768, 1024, 2 ),
			),
			/**
			 * @since 1.5.4
			 *
			 * iPhone 5 (640px x 1136px)
			 */
			array(
				'name'              => 'splash_iphone_5',
				'type'              => 'image',
				'th'                => __( 'iPhone 5', 'iworks-pwa' ),
				'description'       => __( '640px &#10005; 1136px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.4',
				'media'             => array( 320, 568, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPhone 14 Pro 1179×2556
			 */
			array(
				'name'              => 'splash_i_248805b0',
				'type'              => 'image',
				'th'                => __( 'iPhone 14 Pro', 'iworks-pwa' ),
				'description'       => __( '1179px &#10005; 2556px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1179, 2556, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPhone 14 Pro Max (1290×2796)
			 */
			array(
				'name'              => 'splash_i_0fecb7ba',
				'type'              => 'image',
				'th'                => __( 'iPhone 14 Pro', 'iworks-pwa' ),
				'description'       => __( '1290px &#10005; 2796px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1290, 2796, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPhone 14 Plus, 13 Pro Max, 12 Pro Max (1284×2778)
			 *
			 */
			array(
				'name'              => 'splash_i_6d71bfc3',
				'type'              => 'image',
				'th'                => __( 'iPhone 14 Plus, 13 Pro Max, 12 Pro Max', 'iworks-pwa' ),
				'description'       => __( '1284px &#10005; 2778px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1284, 2778, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPhone 14, 13 Pro, 13, 12 Pro, 12 (1170×2532)
			 *
			 */
			array(
				'name'              => 'splash_i_4ced6cf1',
				'type'              => 'image',
				'th'                => __( 'iPhone 14, 13 Pro, 13, 12 Pro, 12', 'iworks-pwa' ),
				'description'       => __( '1170px &#10005; 2532px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1170, 2532, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPad Air 10.9″ (1640×2360)
			 *
			 */
			array(
				'name'              => 'splash_i_6697b877',
				'type'              => 'image',
				'th'                => __( 'iPad Air 10.9″', 'iworks-pwa' ),
				'description'       => __( '1640px &#10005; 2360px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1640, 2360, 2 ),
			),
			/**
			 * @since 1.5.9
			 *
			 * iPad 10.2″ (1620×2160)
			 *
			 */
			array(
				'name'              => 'splash_i_9d7ce6d5',
				'type'              => 'image',
				'th'                => __( 'iPad 10.2″', 'iworks-pwa' ),
				'description'       => __( '1620px &#10005; 2160px', 'iworks-pwa' ),
				'sanitize_callback' => 'intval',
				'max-width'         => 64,
				'group'             => 'apple-touch-startup-image',
				'since'             => '1.5.9',
				'media'             => array( 1620, 2160, 2 ),
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
			'sizes' => '1024x1024',
			'type'  => 'image/png',
			'group' => array(
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
