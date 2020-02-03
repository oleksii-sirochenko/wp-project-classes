<?php


namespace your\space;


class Sidebars {

	function __construct() {

	}

	function hooks() {
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}

	function register_sidebars() {
		$args = array(
			'name' => _x( 'Custom sidebar', 'Name of the category sidebar in the admin area', 'domain' ),
			'id'   => 'custom'
		);
		register_sidebar( $args );
	}

	function r_sidebar( $sidebar_id ) {
		$args = array(
			'sidebar_id' => $sidebar_id,
		);
		Reg::inst()->tmpl->get_template( 'sidebar-custom.php', 'sidebars', $args );
	}
}