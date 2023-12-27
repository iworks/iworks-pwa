<?php
/**
 * iWorks PWA Integrations
 *
 * @since 1.2.0
 */

abstract class iWorks_PWA_Integrations {

    protected $options;

	protected function is_singular_on_front() {
		if ( is_admin() ) {
			return false;
		}
		if ( is_singular() ) {
			return true;
		}
		return false;
	}
}

