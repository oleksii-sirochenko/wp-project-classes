<?php

namespace your\space;

/**
 * AJAX actions register. Controls registration of AJAX actions, population of backend data to frontend side. Has
 * several AJAX validation methods.
 */
class AJAX {
	/**
	 * Array with args for creating the ajax requests.
	 * <code>
	 * array(
	 *     array(
	 *         'action'   => string $ajax_action_name,  // Ajax action.
	 *         'callback' => callable function(){},     // Callback method to handle $action.
	 *         'logged'   => boolean,                   // Is logged?
	 *         'validate' => array or callable
	 *         'sanitize' => array of callable
	 *     )
	 * )
	 * </code>
	 *
	 */
	protected $ajax_actions = array();
	
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
	public function hooks(): void {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * Registers sets of actions and handlers for AJAX.
	 *
	 * @param AJAX_Actions $obj
	 */
	public function add_ajax_actions( AJAX_Actions $obj ): void {
		foreach ( $obj->get_actions() as $action ) {
			$this->ajax_actions[] = $action;
		}
	}
	
	/**
	 * Attaches AJAX data to use in frontend (JS).
	 *
	 * @hooked
	 */
	public function enqueue_scripts(): void {
		if ( $this->is_localized === true ) {
			return;
		}
		
		$config = array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( AJAX_ACTION ),
		);
		
		// Attaches js file to the jquery file which is auto loads with Wordpress.
		$this->is_localized = wp_localize_script( $this->script_name_to_attach_data, AJAX_CONFIG_KEY, $config );
	}
	
	/**
	 * Sets script name where data will be attached.
	 *
	 * @setter
	 *
	 * @param $enqueued_script_name
	 */
	public function set_script_name_to_attach_data( $enqueued_script_name ): void {
		$this->script_name_to_attach_data = $enqueued_script_name;
	}
	
	/**
	 * Loads file with actions and attaches them.
	 */
	public function admin_init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		$this->load_actions();
		$this->attach_actions();
	}
	
	/**
	 * Loads file with predefined AJAX actions and registers them.
	 */
	public function load_actions(): void {
		if ( file_exists( __DIR__ . '/../ajax-init.php' ) ) {
			require __DIR__ . '/../ajax-init.php';
		}
	}
	
	/**
	 * Attaches AJAX actions to WP in its standard way.
	 */
	public function attach_actions(): void {
		$added_actions = array();
		
		foreach ( $this->ajax_actions as $action ) {
			if ( in_array( $action['action'], $added_actions ) ) {
				throw new \InvalidArgumentException( 'There is a key collision between ajax actions. Action:' . $action['action'] );
			}
			
			$ajax_guard     = new AJAX_Guard( $action );
			$guard_callback = array( $ajax_guard, 'guard_action' );
			
			if ( isset( $action['logged'] ) ) {
				if ( ! empty( $action['logged'] ) ) {
					add_action( 'wp_ajax_' . $action['action'], $guard_callback );
				} else {
					add_action( 'wp_ajax_nopriv_' . $action['action'], $guard_callback );
				}
			} else {
				add_action( 'wp_ajax_' . $action['action'], $guard_callback );
				add_action( 'wp_ajax_nopriv_' . $action['action'], $guard_callback );
			}
			
			$added_actions[] = $action['action'];
		}
	}
}