<?php
/**
 * Notice displayed in admin panel.
 */
?>
<div class="notice notice-warning">
	<div class="<?php echo esc_attr( implode( ' ', $args['classes'] ) ); ?>">
		<h4>
		<?php
		if ( ! empty( $args['logo'] ) ) {
			printf( '<span class="iworks-rate-logo" style="background-image:url(%s)"></span>', esc_url( $args['logo'] ) ); }
		?>
		<span><?php printf( '<strong>%s</strong>', $args['title'] ); ?></span></h4>
<?php
/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
$content = __( 'The PWA needs to be hosted on a secure server! Please use HTTPS on your site before activate this plugin.', 'iworks-pwa' );
/**
 * deactivate_plugin
 */
$plugin_file = 'iworks-pwa/iworks-pwa.php';
if ( current_user_can( 'deactivate_plugin', $plugin_file ) ) {
    $content .= PHP_EOL;
    $content .= PHP_EOL;
$deactivate = wp_nonce_url(
    add_query_arg(
        array(
            'action' => 'deactivate',
            'plugin'=> $plugin_file,
        ),
        admin_url( 'plugins.php')
    ),
    'deactivate-plugin_' .$plugin_file,
);
    $content .= sprintf(
        __( 'We recommend <a href="%s">deactivate</a> this plugin and activate it again after this site will use SSL.', 'iworks-pwa' ),
        esc_url( $deactivate )
    );
}
echo wpautop( wp_kses_post( sprintf( $content, sprintf( '<a href="%s#faq" target="_blank">', $args['url'] ), '</a>' ) ) );
?>
		<div class="iworks-rate-buttons">
            <a href="<?php $args['url']; ?> '#faq" target="_blank" class="iworks-rate-button iworks-rate-button--blue"><?php esc_attr_e('FAQ', 'iworks-pwa' ); ?></a>
			<a href="<?php echo $args['support_url']; ?>/#new-post" target="_blank" class="iworks-rate-button iworks-rate-button--green" ><?php echo esc_html( __( 'Get help', 'IWORKS_RATE_TEXTDOMAIN' ) ); ?></a>
		</div>
	</div>
</div>
