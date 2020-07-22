<?php

namespace your\space;

/**
 * Loads scripts and styles in a centralized manner. The idea of this class is to collect all the enqueueing logic into
 * one place and not to make them be scattered across the site. Sometimes it is more convenient to enqueue styles right
 * in a certain class for shortcode or other similar purpose, but it should be as an exception.
 */
class Scripts_Loader {
    /**
     * @var string $min Prefix for assets files to use in production environment.
     */
    protected $min = '';
    protected $directory_uri;
    
    function __construct() {
        $this->directory_uri = get_template_directory_uri();
    }
    
    /**
     * Initializes main object functionality.
     */
    function init() {
        if ( ! SCRIPT_DEBUG ) {
            $this->min = '.min';
        }
    }
    
    /**
     * Attaches methods to hooks.
     */
    function hooks() {
        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets_init' ), 99 );
        } else {
            add_action( 'wp_enqueue_scripts', array( $this, 'assets_init' ), 99999 );
            add_filter( 'style_loader_tag', array( $this, 'remove_unnecessary_attrs' ), 10, 2 );
            add_filter( 'script_loader_tag', array( $this, 'remove_unnecessary_attrs' ), 10, 2 );
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
        }
    }
    
    /**
     * Initializes enqueueing and detaching assets methods for frontend.
     */
    function assets_init() {
        $this->enqueue_styles();
        $this->enqueue_scripts();
        $this->dequeue_scripts();
    }
    
    /**
     * Enqueues styles for frontend side of the site.
     */
    protected function enqueue_styles() {
        // wp_enqueue_style();
    }
    
    /**
     * Enqueues scripts for frontend side of the site.
     */
    protected function enqueue_scripts() {
        // wp_enqueue_script();
        wp_enqueue_script( 'test-ajax', $this->directory_uri . '/assets/js/test' . $this->min . '.js', array( 'jquery' ) );
    }
    
    /**
     * Detaches unnecessary scripts.
     */
    protected function dequeue_scripts() {
        add_action( 'wp_print_footer_scripts', function () {
            // remove files that attaches in footer
            wp_deregister_script( 'wp-embed' );
        } );
        // woocommerce
        // wp_deregister_script( 'jquery-blockui' );
        // wp_deregister_script( 'jquery-cookie' );
        // wp_deregister_script( 'wc-cart-fragments' );
        // wp_deregister_script( 'wc-add-to-cart' );
        // wp_deregister_script( 'woocommerce' );
        // wp_deregister_script( 'selectWoo' );
    }
    
    /**
     * Removes unnecessary script attributes.
     *
     * @param $tag
     * @param $handle
     *
     * @return string Cleared script tag.
     */
    function remove_unnecessary_attrs( $tag, $handle ) {
        $tag = str_replace( array(
            ' type="text/javascript"',
            ' type=\'text/javascript\'',
            ' type="text/css"',
            ' type=\'text/css\'',
        ), '', $tag );
        $tag = preg_replace( "/\sid=['\"][^'\"]*?['\"]\s?/", '', $tag );
        
        return $tag;
    }
    
    /**
     * Initializes enqueueing and detaching assets methods for admin side.
     *
     * @param $suffix
     */
    function admin_assets_init( $suffix ) {
        $this->admin_enqueue_styles( $suffix );
        $this->admin_enqueue_scripts( $suffix );
    }
    
    /**
     * Enqueues styles for admin side of the site.
     *
     * @param string $suffix
     */
    function admin_enqueue_styles( $suffix ) {
        // wp_enqueue_style();
    }
    
    /**
     * Enqueues scripts for admin side of the site.
     *
     * @param string $suffix
     */
    function admin_enqueue_scripts( $suffix ) {
        // wp_enqueue_script();
    }
}