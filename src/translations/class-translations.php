<?php

namespace your\space;

/**
 * Includes plugin translations
 */
class Translations {
	
	public function __construct() {
	
	}
	
	/**
	 * Attaches methods to hooks.
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'load_plugin_text_domain' ), - 1 );
	}
	
	/**
	 * Load plugin text domain on init hook.
	 */
	public function load_plugin_text_domain() {
		load_plugin_textdomain( 'domain', false, basename( PATH ) . '/languages' );
	}
}