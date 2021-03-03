<?php

namespace your\space;

/**
 * General instance for metabox classes.
 */
abstract class Metabox {
    
    /**
     * Should be written in snake case, it maybe used as well in javascript as a property.
     *
     * @var string
     */
    protected $metabox_id = 'custom_metabox';
    
    /**
     * Metabox title
     *
     * @var string
     */
    protected $metabox_title = 'Custom metabox title';
    
    /**
     * Metabox context setting.
     *
     * @var string
     */
    protected $metabox_context = 'advanced';
    
    /**
     * Metabox priority setting.
     *
     * @var string
     */
    protected $metabox_priority = 'default';
    
    /**
     * Property that contains certain post types to which this metabox should be attached
     *
     * @var string[]
     */
    protected $post_types = array( 'post' );
    
    /**
     * Full file name of PHP file which contains main HTML template for metabox.
     *
     * @var string
     */
    protected $template_name = 'custom-metabox.php';
    
    /**
     * Relative path for template folder from the general template folder.
     *
     * @var string
     */
    protected $template_path = 'admin/metaboxes';
    
    /**
     * Custom metabox nonce action.
     *
     * @var string
     */
    protected $nonce_action = 'save_custom_metabox';
    
    /**
     * Custom metabox nonce name.
     *
     * @var string
     */
    protected $nonce_name = 'custom_metabox_nonce';
    
    /**
     * Metabox post meta key.
     */
    const KEY = 'option';
    
    /**
     * Attaches methods to hooks.
     */
    public function hooks() {
        $this->add_meta_boxes_by_post_types();
        $this->save_metaboxes_by_post_types();
        add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_assets' ) );
    }
    
    /**
     * Adds metabox to edit post page for specified post types.
     */
    protected function add_meta_boxes_by_post_types() {
        foreach ( $this->post_types as $post_type ) {
            add_action( 'add_meta_boxes_' . $post_type, array( $this, 'add_custom_meta_box' ) );
        }
    }
    
    /**
     * Saves metabox data to post in specified post types edit page.
     */
    protected function save_metaboxes_by_post_types() {
        foreach ( $this->post_types as $post_type ) {
            add_action( 'save_post_' . $post_type, array( $this, 'save_metabox' ) );
        }
    }
    
    /**
     * Enqueues script and styles on edit page.
     */
    public function wp_enqueue_assets() {
    
    }
    
    /**
     * Checks is current editing page is for listed post types.
     *
     * @return bool
     */
    public function is_current_post_type_editing_page() {
        $screen = get_current_screen();
        
        return in_array( $screen->base, $this->post_types );
    }
    
    /**
     * Registers metabox to render on edit page.
     */
    public function add_custom_meta_box() {
        add_meta_box( $this->metabox_id, $this->metabox_title, array(
            $this,
            'render_custom_metabox'
        ), null, $this->metabox_context, $this->metabox_priority );
    }
    
    /**
     * Saves data from metabox.
     *
     * @param $post_id
     */
    public function save_metabox( $post_id ) {
        if ( ! $this->validate_request() ) {
            return;
        }
    }
    
    /**
     * Validates save data request before actual save.
     *
     * @return bool
     */
    public function validate_request() {
        return isset( $_POST[ $this->nonce_name ] ) &&
               wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) &&
               current_user_can( 'manage_options' );
    }
    
    /**
     * Renders HTML template for metabox.
     *
     * @throws \Exception
     */
    public function render_custom_metabox() {
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
     * This method should return required arguments in array for metabox template.
     * Here we can get post metas and other data and put it to the template.
     *
     * @return array
     */
    public function get_template_args() {
        global $post;
        
        $args = array();
        
        return $args;
    }
    
    /**
     * Returns metabox saved data of post by id.
     *
     * @param $id
     *
     * @return array
     */
    protected function get_meta( $id ) {
        $post_meta = get_post_meta( $id, static::OPTION, true );
        if ( ! is_array( $post_meta ) ) {
            return array();
        }
        
        return $post_meta;
    }
}