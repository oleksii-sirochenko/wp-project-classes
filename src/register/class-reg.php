<?php


namespace your\space;

/**
 * Registry that controls classes
 */
final class Reg {
	protected static $instance;
	public $scripts_loader;
	public $tmpl;
	public $ajax;

	protected function __construct() {
		$this->tmpl           = new Template_Loader( $this->get_theme_directory() );
		$this->scripts_loader = new Scripts_Loader();
		$this->init_ajax();

		if ( is_admin() ) {

		}
	}

	static function inst() {
		if ( isset( self::$instance ) ) {
			return self::$instance;
		} else {
			self::$instance = new self();
			self::$instance->init();

			return self::$instance;
		}
	}

	protected function get_theme_directory() {
		return get_stylesheet_directory();
	}

	protected function get_plugin_directory() {
		return dirname( __FILE__, 3 );
	}

	protected function init() {
		$this->run_initializing_methods_on_objects();
		$this->run_hooks_on_objects();
		$this->add_debug_method();
	}

	/**
	 * Registry invokes certain methods on each object that has this methods. Usually other objects
	 * want to attach to hooks or run initialization function after creation.
	 *
	 * In this case init it is common setup of the object and hooks it is dedicated to setup handlers
	 * for wordpress hooks
	 */
	protected function run_initializing_methods_on_objects() {
		foreach ( $this as $object ) {
			if ( is_object( $object ) ) {
				$this->run_initializing_methods_on_object( $object, array( 'init', 'hooks' ) );
			}
		}
	}

	protected function run_initializing_methods_on_object( $object, array $methods ) {
		foreach ( $methods as $method ) {
			if ( method_exists( $object, $method ) ) {
				$object->$method();
			}
		}
	}

	/**
	 * calls initializing hooks on objects that don't mean to be reused by calling Register. For example you have object with
	 * that can do something but its logic invokes only by specific hooks and you don't need to have instance of this
	 * object in Register property. So you simply add this object here and you are free of additional allocation of new property
	 * inside of Register
	 */
	protected function run_hooks_on_objects() {
		$objects = array();

		if ( is_admin() ) {

		}

		foreach ( $objects as $object ) {
			$this->run_initializing_methods_on_object( $object, array( 'hooks' ) );
		}
	}

	protected function init_ajax() {
		$this->ajax = new AJAX();
		$this->ajax->add_front_ajax_actions( new Front_Page_AJAX_Actions() );
	}

	public function is_localhost() {
		return getenv( 'is_localhost' ) == 'true';
	}

	protected function add_debug_method() {
		if ( current_user_can( 'manage_options' ) || $this->is_localhost() ) {
			add_action( 'wp_head', function () {
				$this->test();
			} );
		}
	}

	protected function test() {

	}
}