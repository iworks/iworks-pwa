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
				'name'              => 'app_',
				'type'              => 'text',
				'class'             => 'regular-text',
				'th'                => __( '', 'iworks-pwa' ),
				'description'       => __( '', 'iworks-pwa' ),
				'sanitize_callback' => 'esc_html',
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Colors', 'iworks-pwa' ),
			),
			array(
				'name'              => 'color_bg',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Background Color', 'iworks-pwa' ),
				'description'       => __( 'Background color of the splash screen.', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
				'use_name_as_id'    => true,
			),
			array(
				'name'              => 'color_theme',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Theme Color', 'iworks-pwa' ),
				'description'       => __( 'Theme color is used on supported devices to tint the UI elements of the browser and app switcher. When in doubt, use the same color as Background Color.', 'iworks-pwa' ),
				'default'           => '#d5e0eb',
				'sanitize_callback' => 'esc_html',
				'use_name_as_id'    => true,
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Pages', 'iworks-pwa' ),
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
	return $options;
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