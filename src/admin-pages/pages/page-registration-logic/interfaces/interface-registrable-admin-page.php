<?php

namespace your\space;

interface Registrable_Admin_Page {
    function __construct();
    
    /**
     * Sanitizes data from current settings page before save
     *
     * @param array $data
     *
     * @return array
     */
    function sanitize_form_data( array $data );
    
    /**
     * render method for current page template
     */
    function r_tmpl();
    
    /**
     * @return string
     */
    function get_option_key();
    
    /**
     * @return array
     */
    function get_options();
    
    function set_options( $options );
    
    /**
     * returns value by specified property with default value when it is not set
     *
     * @param string $property
     * @param string $default
     *
     * @return mixed
     */
    function get_options_item( $property, $default = '' );
    
    /**
     * sets option value by specified option key
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    function set_option_item( $key, $value );
    
    function get_page_slug();
    
    function get_parent_page_slug();
    
    function get_menu_title();
    
    function get_page_title();
    
    /**
     * @return string required user capability to edit this page
     */
    function get_capability();
    
    function get_icon_url();
    
    /**
     * @return string menu position of the page
     */
    function get_position();
    
    /**
     *
     * @return boolean
     */
    function is_current_page();
    
    function get_page_url();
}