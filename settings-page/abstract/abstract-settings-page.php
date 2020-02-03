<?php


namespace your\space;

/**
 * abstract singleton, inherited class has to implement __constuct as protected
 */
abstract class Settings_Page {
	protected static $instances = array();
	protected $option_key = 'option_key';
	protected $page_slug = 'page_slug';
	protected $page_title = 'page title';
	protected $menu_title = 'menu_title';
	protected $capability = 'manage_options';
	protected $icon_url = 'dashicons-admin-settings';

	abstract protected function __construct();

	static function inst() {
		$called_class = get_called_class();

		if ( ! isset( self::$instances[ $called_class ] ) ) {
			self::$instances[ $called_class ] = new $called_class();
		}

		return self::$instances[ $called_class ];
	}

	function hooks() {
		add_action( 'admin_menu', array( $this, 'create_page' ), 11 );
		add_action( 'admin_init', array( $this, 'register_fields' ) );
	}

	function create_page() {
		add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->page_slug,
			array( $this, 'r_page_template' ), $this->icon_url );
	}

	function register_fields() {
		register_setting( $this->option_key, $this->option_key, array(
			$this,
			'sanitize_page'
		) );
	}

	function sanitize_page( $data ) {
		return $data;
	}

	abstract function r_page_template();

	function get_option_key() {
		return $this->option_key;
	}

	function get_options() {
		return get_option( $this->option_key, array() );
	}

	function set_options( $options ) {
		return update_option( $this->option_key, $options );
	}

	function get_options_item( $property, $default = '' ) {
		$options = $this->get_options();

		if ( is_array( $options ) && ! empty( $options ) &&
		     isset( $options[ $property ] ) && ! empty( $options[ $property ] ) ) {
			return $options[ $property ];
		}

		return $default;
	}

	function set_option_item( $key, $value ) {
		$options = $this->get_options();

		if ( is_array( $options ) ) {
			$options[ $key ] = $value;
		} else {
			$options = array( $key => $value );
		}

		return $this->set_options( $options );
	}

	function get_page_slug() {
		return $this->page_slug;
	}

	function get_menu_title() {
		return $this->menu_title;
	}

	function get_page_title() {
		return $this->page_title;
	}

	function is_current_page() {
		return isset( $_GET['page'] ) && $_GET['page'] == $this->page_slug && ! isset( $_GET['tab'] );
	}
}