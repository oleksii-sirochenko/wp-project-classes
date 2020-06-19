<?php

namespace your\space;

class AJAX {
    /**
     * AJAX response status constant.
     */
    const STATUS_SUCCESS = 'success';
    
    /**
     * AJAX response status constant.
     */
    const STATUS_ERROR = 'error';
    
    /**
     * Javascript, browser's window object property with the help of which backend sends useful data to frontend (JS).
     */
    const KEY = 'wp_ajax_config';
    
    /**
     * @var AJAX_Actions[]
     */
    protected $ajax_actions_sets = array();
    
    /**
     * Array with args for creating the ajax requests.
     * <code>
     * array(
     *   array(
     *    'action'    =>  string $ajax_action_name,  // Ajax action.
     *    'function'  =>  callable function(){},     // Callback method to handle $action.
     *    'logged'    =>  boolean,                   // Is logged?
     *   )
     * )
     * </code>
     *
     */
    protected $ajax_actions = array();
    
    /**
     * Scripts data divided by frontend type.
     *
     * @var array
     */
    protected $scripts_data = array(
        'front' => array(),
        'admin' => array(),
    );
    
    /**
     * Name of the script to which will be attached custom scripts data for frontend use.
     *
     * @var string
     */
    protected $script_name_to_attach_data = 'jquery';
    
    /**
     * Identifies whether data from property 'scripts_data' was attached to frontend.
     *
     * @var bool
     */
    protected $is_localized = false;
    
    public function __construct() {
    }
    
    /**
     * Attaches methods to hooks.
     */
    public function hooks() {
        add_action( 'admin_init', array( $this, 'attach_actions' ) );
        
        switch ( $this->get_side() ) {
            case 'front':
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                break;
            case 'admin':
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                break;
        }
    }
    
    /**
     * Returns type of the frontend side visited by user.
     *
     * @return string
     */
    protected function get_side() {
        static $side;
        if ( empty( $side ) ) {
            if ( is_admin() && ! wp_doing_ajax() ) {
                $side = 'admin';
            } else {
                $side = 'front';
            }
        }
        
        return $side;
    }
    
    /**
     * Registers sets of actions and handlers for AJAX.
     *
     * @param AJAX_Actions $obj
     */
    public function add_ajax_actions( AJAX_Actions $obj ) {
        $this->ajax_actions_sets[] = $obj;
        $this->ajax_actions        = array_merge( array(), $this->ajax_actions, $obj->get_actions() );
    }
    
    /**
     * Adds custom backend data to certain frontend side.
     *
     * @param string $side - possible values 'front','admin'.
     * @param array  $data
     */
    public function add_scripts_data( $side, array $data ) {
        if ( empty( $data ) ) {
            return;
        }
        foreach ( $data as $key => $value ) {
            $this->scripts_data[ $side ][ $key ] = $value;
        }
    }
    
    /**
     * Attaches custom backed data 'scripts_data' to use in frontend (JS).
     */
    public function enqueue_scripts() {
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
        
        $this->set_scripts_data();
        
        foreach ( $this->scripts_data[ $this->get_side() ] as $key => $value ) {
            if ( ! isset( $config_array[ $key ] ) ) {
                $config_array[ $key ] = $value;
            }
        }
        
        // attach js file to the jquery file which is auto loads with wordpress
        $this->is_localized = wp_localize_script( $this->script_name_to_attach_data, self::KEY, $config_array );
    }
    
    /**
     * Sets scripts data by invoking callbacks. This method runs during actions 'wp_enqueue_scripts',
     * 'admin_enqueue_scripts' where all conditional tags are evaluated and the current page is defined. Therefore
     * AJAX actions sets can rely on it and properly check current page whether to add data or not.
     */
    protected function set_scripts_data() {
        foreach ( $this->ajax_actions_sets as $ajax_actions ) {
            $this->add_scripts_data( $this->get_side(), $ajax_actions->get_scripts_data() );
        }
    }
    
    /**
     * @setter
     *
     * @param $enqueued_script_name
     */
    public function set_script_name_to_attach_data( $enqueued_script_name ) {
        $this->script_name_to_attach_data = $enqueued_script_name;
    }
    
    /**
     * Attaches AJAX actions to WP in it's standard way.
     */
    public function attach_actions() {
        $added_actions = array();
        foreach ( $this->ajax_actions as $action ) {
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
    
    /**
     * Returns success response for AJAX request.
     *
     * @param mixed  $data
     * @param string $message
     *
     * @return array
     */
    public static function get_success_response( $data = null, $message = '' ) {
        return self::get_response_with_status( self::STATUS_SUCCESS, $data, $message );
    }
    
    /**
     * Returns error response for AJAX request.
     *
     * @param mixed  $data
     * @param string $message
     *
     * @return array
     */
    public static function get_error_response( $data = null, $message = '' ) {
        return self::get_response_with_status( self::STATUS_ERROR, $data, $message );
    }
    
    /**
     * Returns AJAX response with status, nonce, custom data and response message.
     *
     * @param string $status
     * @param mixed  $data
     * @param string $message
     *
     * @return array
     */
    protected static function get_response_with_status( $status, $data = null, $message = '' ) {
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
     * Validates AJAX request. Should be used in AJAX handlers before process the request.
     */
    public static function validate_request() {
        $nonce = $_POST['nonce'];
        
        if ( ! wp_verify_nonce( $nonce, self::KEY ) ) {
            wp_send_json( self::get_error_response( null, 'Unauthorized request!' ) );
        }
    }
    
    /**
     * Validates AJAX request in admin frontend side. Should be used in AJAX handles on the admin side before process
     * the request.
     */
    public static function validate_admin_side_request() {
        self::validate_request();
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json( self::get_error_response( null, 'Unauthorized request!' ) );
        }
    }
}
