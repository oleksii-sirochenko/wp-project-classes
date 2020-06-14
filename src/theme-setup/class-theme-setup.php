<?php

namespace your\space;


class Theme_Setup {
    function __construct() {
        
    }
    
    function hooks() {
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        $this->unhook_rest_api();
        $this->unhook_bloated_things();
    }
    
    function unhook_rest_api() {
        remove_action( 'template_redirect', 'rest_output_link_header', 11 );
        // Remove the REST API lines from the HTML Header
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        // Remove the REST API endpoint.
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    }
    
    function unhook_bloated_things() {
        add_filter( 'use_block_editor_for_post', '__return_false' );
        // Turn off oEmbed auto discovery.
        add_filter( 'embed_oembed_discover', '__return_false' );
        // Don't filter oEmbed results.
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
        // Remove oEmbed discovery links.
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        // Remove oEmbed-specific JavaScript from the front-end and back-end.
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wc_gallery_noscript' );
        add_filter( 'the_generator', '__return_empty_string' );
        add_filter( 'get_the_generator_html', '__return_empty_string' );
        add_filter( 'get_the_generator_xhtml', '__return_empty_string' );
        add_filter( 'get_the_generator_atom', '__return_empty_string' );
        add_filter( 'get_the_generator_rss2', '__return_empty_string' );
        add_filter( 'get_the_generator_comment', '__return_empty_string' );
        add_filter( 'get_the_generator_export', '__return_empty_string' );
        add_filter( 'wf_disable_generator_tags', '__return_empty_string' );
    }
    
    function after_setup_theme() {
        $this->load_theme_textdomain();
        $this->add_image_sizes();
        $this->add_theme_support();
    }
    
    protected function load_theme_textdomain() {
        load_theme_textdomain( 'domain', get_stylesheet_directory() . '/languages' );
    }
    
    protected function add_image_sizes() {
        add_image_size( 'article', 320, 240, true );
    }
    
    protected function add_theme_support() {
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
    }
}