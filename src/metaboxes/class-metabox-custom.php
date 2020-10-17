<?php

namespace your\space;

/**
 * Registers metabox, renders HTML template and saves provided data to post where this metabox is attached.
 */
class Metabox_Custom extends Metabox {
    
    /**
     * Should be written in snake case, it maybe used as well in javascript as a property.
     *
     * @var string
     */
    protected $metabox_id = 'metabox_custom';
    
    /**
     * Full file name of PHP file which contains main HTML template for metabox.
     *
     * @var string
     */
    protected $template_name = 'metabox-custom.php';
    
    /**
     * Custom metabox nonce action.
     *
     * @var string
     */
    protected $nonce_action = 'save_metabox_custom';
    
    /**
     * Custom metabox nonce name.
     *
     * @var string
     */
    protected $nonce_name = 'metabox_custom_nonce';
    
    /**
     * Property that contains certain post types to which this metabox should be attached
     *
     * @var string[]
     */
    protected $post_types = array( 'page' );
    
    /**
     * Metabox post meta key.
     */
    const KEY = 'metabox_custom_option';
    
    public function __construct() {
        $this->metabox_title = __( 'Metabox title', 'domain' );
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
        
        update_post_meta( $post_id, static::KEY, $_POST[ static::KEY ] );
    }
    
    /**
     * This method should return required arguments in array for metabox template.
     * Here we can get post metas and other data and put it to the template.
     *
     * @return array
     */
    public function get_template_args() {
        global $post;
        
        $args = array(
            'meta_key' => static::KEY,
            'meta'     => $this->get_meta( $post->ID ),
        );
        
        return $args;
    }
}