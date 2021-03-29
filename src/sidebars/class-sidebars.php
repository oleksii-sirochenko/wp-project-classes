<?php


namespace your\space;

/**
 *  Registrator of WP sidebars. All custom sidebars should be registered here and any other related logic.
 */
class Sidebars {
	
	function __construct() {
	
	}
	
	/**
	 * Attaches methods to hooks.
	 */
	function hooks() {
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}
	
	/**
	 * Registers custom sidebars.
	 */
	function register_sidebars() {
		$args = array(
			'name' => _x( 'Custom sidebar', 'Name of the category sidebar in the admin area', 'domain' ),
			'id'   => 'custom'
		);
		register_sidebar( $args );
	}
	
	/**
	 * Renders HTML template of sidebar by provided ID.
	 *
	 * @param $sidebar_id
	 */
	function r_sidebar( $sidebar_id ) {
		$args = array(
			'sidebar_id' => $sidebar_id,
		);
		Reg::inst()->tmpl->get_template( 'sidebar-custom.php', 'sidebars', $args );
	}
}