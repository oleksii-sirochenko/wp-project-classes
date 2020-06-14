<?php


namespace your\space;

abstract class Metabox {
    /**
     * should be written in snake case, it maybe used as well in javascript as a property.
     *
     * @var string
     */
    protected $metabox_id = 'custom_metabox';
    protected $metabox_title = 'Custom metabox title';
    protected $metabox_context = 'advanced';
    protected $metabox_priority = 'default';
    /**
     * property that contains certain post types to which this metabox should be attached
     *
     * @var string[]
     */
    protected $post_types = array( 'post' );
    /**
     * template name with extension, example: custom-metabox.php
     *
     * @var string
     */
    protected $template_name = 'custom-metabox.php';
    protected $template_path = 'admin/metaboxes';
    protected $nonce_action = 'save_custom_metabox';
    protected $nonce_name = 'custom_metabox_nonce';
    const OPTION = 'option';
    
    function hooks() {
        $this->add_meta_boxes_by_post_types();
        $this->save_metaboxes_by_post_types();
        $this->wp_enqueue_scripts();
    }
    
    protected function add_meta_boxes_by_post_types() {
        foreach ( $this->post_types as $post_type ) {
            add_action( 'add_meta_boxes_' . $post_type, array( $this, 'add_custom_meta_box' ) );
        }
    }
    
    protected function save_metaboxes_by_post_types() {
        foreach ( $this->post_types as $post_type ) {
            add_action( 'save_post_' . $post_type, array( $this, 'save_metabox' ) );
        }
    }
    
    function wp_enqueue_scripts() {
    
    }
    
    function is_current_post_type_editing_page() {
        $screen = get_current_screen();
        
        return in_array( $screen->base, $this->post_types );
    }
    
    function add_custom_meta_box() {
        add_meta_box( $this->metabox_id, $this->metabox_title, array(
            $this,
            'render_custom_metabox'
        ), null, $this->metabox_context, $this->metabox_priority );
    }
    
    function save_metabox( $post_id ) {
        if ( ! $this->validate_request() ) {
            return;
        }
    }
    
    function validate_request() {
        return isset( $_POST[ $this->nonce_name ] ) &&
               wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) &&
               current_user_can( 'manage_options' );
    }
    
    function render_custom_metabox() {
        wp_nonce_field( $this->nonce_action, $this->nonce_name );
        
        $args = $this->get_template_args();
        if ( ! is_array( $args ) ) {
            $e = new \Exception( static::CLASS . '::get_template_args() should return array()' );
            throw( $e );
            
            return;
        }
        
        Reg::inst()->tmpl->get_template( $this->template_name, $this->template_path, $args );
    }
    
    /**
     * This method should return args in array that required by metabox template.
     * Here we can apply to post meta to get data and put it to the template
     *
     * @return array
     */
    function get_template_args() {
        global $post;
        
        $args = array();
        
        return $args;
    }
    
    /**
     * @param $id
     *
     * @return array
     */
    static function get_option( $id ) {
        $post_meta = get_post_meta( $id, static::OPTION, true );
        if ( ! is_array( $post_meta ) ) {
            return array();
        }
        
        return $post_meta;
    }
}