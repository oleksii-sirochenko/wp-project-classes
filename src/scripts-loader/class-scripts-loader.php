<?php
/**
 * class that loads scripts and styles for theme or plugin
 */

namespace your\space;


class Scripts_Loader {
    /**
     * @var string $min prefix to use in production environment
     */
    protected $min = '';
    protected $directory_uri;
    
    function __construct() {
        $this->directory_uri = get_template_directory_uri();
    }
    
    function init() {
        if ( ! Reg::inst()->is_localhost() ) {
            $this->min = '.min';
        }
    }
    
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
    
    function assets_init() {
        $this->enqueue_styles();
        $this->enqueue_scripts();
        $this->dequeue_scripts();
    }
    
    protected function enqueue_styles() {
        // wp_enqueue_style();
        
    }
    
    protected function enqueue_scripts() {
        // wp_enqueue_script();
        wp_enqueue_script( 'test-ajax', $this->directory_uri . '/assets/js/test' . $this->min . '.js', array( 'jquery' ) );
    }
    
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
    
    function admin_assets_init( $suffix ) {
        $this->admin_enqueue_styles( $suffix );
        $this->admin_enqueue_scripts( $suffix );
    }
    
    function admin_enqueue_styles( $suffix ) {
        // wp_enqueue_style();
    }
    
    function admin_enqueue_scripts( $suffix ) {
        // wp_enqueue_script();
    }
    
}