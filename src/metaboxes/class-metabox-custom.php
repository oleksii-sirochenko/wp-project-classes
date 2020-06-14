<?php


namespace your\space;


class Metabox_Custom extends Metabox {
    protected $metabox_id = 'metabox_custom';
    protected $metabox_title = 'Metabox title';
    protected $template_name = 'metabox-custom.php';
    protected $nonce_action = 'save_metabox_custom';
    protected $nonce_name = 'metabox_custom_nonce';
    protected $post_types = array( 'page' );
    const OPTION = 'metabox_custom_option';
    
    function save_metabox( $post_id ) {
        if ( ! $this->validate_request() ) {
            return;
        }
        
        update_post_meta( $post_id, static::OPTION, $_POST[ static::OPTION ] );
    }
    
    function get_template_args() {
        global $post;
        
        $args = array(
            'option_key' => static::OPTION,
            'options'    => self::get_option( $post->ID ),
        );
        
        return $args;
    }
}