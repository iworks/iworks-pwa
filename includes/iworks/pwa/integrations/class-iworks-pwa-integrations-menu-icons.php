<?php
/**
 * Menu Icons by ThemeIsle
 * https://wordpress.org/plugins/menu-icons/
 *
 * @since 1.4.0
 */
class iWorks_PWA_Integrations_Menu_Icons extends iWorks_PWA_Integrations {

    public function __construct( $options ) {
        add_filter( 'iworks_pwa_manifest_shortcut_element', array( $this, 'filter_iworks_pwa_manifest_shortcut_element_maybe_add_icon' ), 10, 2 );
		/**
		 * set options
		 */
		$this->options = $options;
    }

    public function filter_iworks_pwa_manifest_shortcut_element_maybe_add_icon( $element, $menu_item ) {
        $icon = get_post_meta( $menu_item->ID, 'menu-icons', true );
        if ( empty ( $icon ) || ! is_array( $icon ) ) {
            return $element;
        }
        if ( ! isset( $icon['type'] ) || 'image' !== $icon['type'] ) {
            return $element;
        }
        if ( ! isset( $icon['icon'] ) || empty( $icon['icon'] ) ) {
            return $element;
        }
        $src = wp_get_attachment_image_src($icon['icon'], 'full' );
        if ( empty( $src ) ) {
            return $element;
        }
        $element['icons'] = array(
            array(
                'src' => wp_make_link_relative( $src[0] ),
                'sizes' => sprintf( '%dx%d', $src[1], $src[2] ),
            ),
        );
        return $element;
    }

}

