<?php

namespace your\space;

/**
 * Registers native navigational menus. Other logic for these menus should be placed here.
 */
class Nav_Menus {
	function __construct() {
	
	}
	
	/**
	 * Attaches methods to hooks.
	 */
	function hooks() {
		add_action( 'after_setup_theme', array( $this, 'register_nav_menus' ) );
	}
	
	/**
	 * Registers nav menus.
	 */
	function register_nav_menus() {
		register_nav_menus( array(
			'header_menu' => __( 'Menu in header', 'domain' ),
			'footer_menu' => __( 'Menu in footer', 'domain' ),
		) );
	}
}