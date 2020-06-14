<?php


namespace your\space;


class Nav_Menus {
    function __construct() {
    
    }
    
    function hooks() {
        add_action( 'after_setup_theme', array( $this, 'register_nav_menus' ) );
    }
    
    function register_nav_menus() {
        register_nav_menus( array(
            'header_menu' => __( 'Menu in header', 'domain' ),
            'footer_menu' => __( 'Menu in footer', 'domain' ),
        ) );
    }
}