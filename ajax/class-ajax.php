<?php

namespace your\space;

class AJAX {
	const STATUS_SUCCESS = 'success';
	const STATUS_ERROR = 'error';
	const KEY = 'wp_ajax_config';
	/**
	 * Array with args for creating the ajax requests
	 * <code>
	 * array(
	 *   array(
	 *    'action'  => 'ajax_action_name', // Ajax action
	 *    'function'=> function(){}, // Callback method to handle $action
	 *    'logged'  => boolean, // Is logged?
	 *   )
	 * )
	 * </code>
	 * }
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

	function init() {
		$this->set_actions( $this->ajax_actions );
	}

	function hooks() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	protected function get_side() {
		if ( is_admin() ) {
			return 'admin';
		}

		return 'front';
	}

	function add_front_ajax_actions( AJAX_Actions $obj ) {
		$this->ajax_actions += $obj->get_actions();
	}

	function add_admin_ajax_actions( AJAX_Actions $obj ) {
		if ( is_admin() ) {
			foreach ( $obj->get_actions() as $key => $action ) {
				$action['logged']           = true;
				$this->ajax_actions[ $key ] = $action;
			}
		}
	}

	/**
	 * @param $key string
	 * @param $value array|int|object|string
	 */
	function add_front_scripts_data( $key, $value ) {
		$this->scripts_data['front'][ $key ] = $value;
	}

	/**
	 * @param $key string
	 * @param $value array|int|object|string
	 */
	function add_admin_scripts_data( $key, $value ) {
		$this->scripts_data['admin'][ $key ] = $value;
	}

	function enqueue_scripts() {
		if ( isset( $this->is_localized ) && $this->is_localized === true ) {
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

	protected function set_actions( array $actions ) {
		foreach ( $actions as $action ) {
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
