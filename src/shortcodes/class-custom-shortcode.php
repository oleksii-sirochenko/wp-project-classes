<?php

namespace your\space;

/**
 * Handles WP shortcode logic and renders HTML template.
 */
class Custom_Shortcode extends Shortcode {
    
    public function __construct() {
        
    }
    
    /**
     * Returns shortcode name.
     *
     * @return string
     */
    public function get_shortcode_name() {
        return 'your_space_custom_shortcode';
    }
    
    /**
     * Invokes main logic for current class.
     *
     * @param array  $atts
     * @param string $content
     * @param string $tag
     *
     * @return string HTML template.
     */
    public function do_shortcode( $atts, $content, $tag ) {
        if ( ! is_user_logged_in() ) {
        
        }
        
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        
        $args = array();
        
        return Reg::inst()->tmpl->get_template_as_string( 'custom-shortcode.php', 'shortcodes/custom-shortcode', $args );
    }
    
    /**
     * Enqueues styles required for current shortcode.
     */
    protected function enqueue_styles() {
        wp_register_style(
            'your_space_custom_shortcode',
            ASSETS_URL . '/dist/css/shortcodes/custom-shortcode.css'
        );
        
        wp_print_styles( array(
            'your_space_custom_shortcode',
        ) );
    }
    
    /**
     * Enqueues scripts required for current shortcode.
     */
    protected function enqueue_scripts() {
        wp_enqueue_script(
            'your_space_shortcode',
            ASSETS_URL . '/dist/js/shortcodes/custom-shortcode' . MIN . '.js',
            array( 'jquery' ),
            1,
            true
        );
        
        wp_localize_script( 'your_space_shortcode', 'your_space_shortcode', array() );
    }
}