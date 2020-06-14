<?php

namespace your\space;

class AJAX {
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const KEY = 'wp_ajax_config';
    /**
     * @var AJAX_Actions[]
     */
    protected $ajax_actions_sets = array();
    /**
     * array with args for creating the ajax requests
     * <code>
     * array(
     *   array(
     *    'action'    =>  string $ajax_action_name,  // Ajax action
     *    'function'  =>  callable function(){},     // Callback method to handle $action
     *    'logged'    =>  boolean,                   // Is logged?
     *   )
     * )
     * </code>
     *
     */
    protected $ajax_actions = array();
    protected $scripts_data = array(
        'front' => array(),
        'admin' => array(),
    );
    protected $script_name_to_attach_data = 'jquery';
    protected $is_localized = false;
    
    function __construct() {
    }
    
    function hooks() {
        switch ( $this->get_side() ) {
            case 'front':
                add_action( 'wp', array( $this, 'set_actions' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                break;
            case 'admin':
                add_action( 'admin_init', array( $this, 'set_actions' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                break;
        }
    }
    
    protected function get_side() {
        static $side;
        if ( empty( $side ) ) {
            if ( is_admin() ) {
                $side = 'admin';
            } else {
                $side = 'front';
            }
        }
        
        return $side;
    }
    
    function add_front_ajax_actions( AJAX_Actions $obj ) {
        $this->ajax_actions_sets[] = $obj;
    }
    
    function add_admin_ajax_actions( AJAX_Actions $obj ) {
        if ( $this->get_side() === 'admin' ) {
            $this->ajax_actions_sets[] = $obj;
        }
    }
    
    function add_front_scripts_data( AJAX_Actions $obj ) {
        $this->add_scripts_data( 'front', $obj->get_scripts_data() );
    }
    
    function add_admin_scripts_data( AJAX_Actions $obj ) {
        $this->add_scripts_data( 'admin', $obj->get_scripts_data() );
    }
    
    /**
     * @param string $side - possible values 'front','admin'
     * @param array  $data
     */
    function add_scripts_data( $side, array $data ) {
        if ( empty( $data ) ) {
            return;
        }
        foreach ( $data as $key => $value ) {
            $this->scripts_data[ $side ][ $key ] = $value;
        }
    }
    
    function enqueue_scripts() {
        if ( $this->is_localized === true ) {
            return;
        }
        
        $config_array = array(
            'url'   => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( self::KEY ),
        );
        
        foreach ( $this->ajax_actions as $ajax_action ) {
            $config_array['actions'][ $ajax_action['action'] ] = $ajax_action['action'];
        }
        
        foreach ( $this->scripts_data[ $this->get_side() ] as $key => $value ) {
            if ( ! isset( $config_array[ $key ] ) ) {
                $config_array[ $key ] = $value;
            }
        }
        
        // attach js file to the jquery file which is auto loads with wordpress
        $this->is_localized = wp_localize_script( $this->script_name_to_attach_data, self::KEY, $config_array );
    }
    
    function set_script_name_to_attach_data( $enqueued_script_name ) {
        $this->script_name_to_attach_data = $enqueued_script_name;
    }
    
    function set_actions() {
        /**
         * @var AJAX_Actions $ajax_actions_set
         */
        foreach ( $this->ajax_actions_sets as $ajax_actions_set ) {
            $this->ajax_actions = array_merge( array(), $this->ajax_actions, $ajax_actions_set->get_actions() );
        }
        
        if ( $this->get_side() === 'admin' ) {
            foreach ( $this->ajax_actions as $key => $ajax_action ) {
                $this->ajax_actions[ $key ]['logged'] = true;
            }
        }
        
        $this->attach_actions( $this->ajax_actions );
    }
    
    protected function attach_actions( array $actions ) {
        $added_actions = array();
        foreach ( $actions as $action ) {
            if ( in_array( $action['action'], $added_actions ) ) {
                throw new \InvalidArgumentException( 'There is a key collision between ajax actions' );
            }
            if ( isset( $action['logged'] ) ) {
                if ( ! empty( $action['logged'] ) ) {
                    add_action( 'wp_ajax_' . $action['action'], $action['function'] );
                } else {
                    add_action( 'wp_ajax_nopriv_' . $action['action'], $action['function'] );
                }
            } else {
                add_action( 'wp_ajax_' . $action['action'], $action['function'] );
                add_action( 'wp_ajax_nopriv_' . $action['action'], $action['function'] );
            }
            $added_actions[] = $action['action'];
        }
    }
    
    static function get_success_response( $data = null, $message = null ) {
        return self::get_response_with_status( self::STATUS_SUCCESS, $data, $message );
    }
    
    static function get_error_response( $data = null, $message = null ) {
        return self::get_response_with_status( self::STATUS_ERROR, $data, $message );
    }
    
    protected static function get_response_with_status( $status, $data = null, $message = null ) {
        $response = array(
            'status' => $status,
            'nonce'  => wp_create_nonce( self::KEY ),
            'data'   => $data,
        );
        
        if ( ! empty( $message ) ) {
            $response['message'] = $message;
        }
        
        return $response;
    }
    
    /**
     * should be used in AJAX handlers before process the request
     */
    static function validate_request() {
        $nonce = $_POST['nonce'];
        
        if ( ! wp_verify_nonce( $nonce, self::KEY ) ) {
            wp_send_json( self::get_error_response( null, 'Unauthorized request!' ) );
        }
    }
    
    /**
     * should be used in AJAX handles on the admin side before process the request
     */
    static function validate_admin_side_request() {
        self::validate_request();
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json( self::get_error_response( null, 'Unauthorized request!' ) );
        }
    }
}
