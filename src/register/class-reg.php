<?php

namespace your\space;

/**
 * Registry is a final singleton class that controls classes.
 */
class Reg {
	/**
	 * Singleton instance.
	 *
	 * @var Reg
	 */
	protected static $instance;
	
	/**
	 * Template loader that loads PHP/HTML template files.
	 *
	 * @var Template_Loader
	 */
	public $tmpl;
	
	protected function __construct() {
		$this->tmpl = new Template_Loader( PATH );
		
		if ( is_admin() ) {
		
		}
	}
	
	/**
	 * Method that returns singleton instance.
	 *
	 * @return Reg
	 */
	static function inst(): Reg {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
			static::$instance->init();
		}
		
		return static::$instance;
	}
	
	/**
	 * Initializes object once per load.
	 */
	protected function init(): void {
		$this->run_initializing_methods_on_objects();
		$this->run_hooks_on_objects();
	}
	
	/**
	 * Registry invokes certain methods on each object that has this methods. Usually other objects
	 * want to attach their methods to specific hooks or run initialization function after creation.
	 *
	 * In this case init it is common setup of the object and hooks it is dedicated to setup handlers
	 * for wordpress hooks
	 */
	protected function run_initializing_methods_on_objects(): void {
		foreach ( $this as $object ) {
			if ( is_object( $object ) ) {
				$this->run_initializing_methods_on_object( $object, array( 'init', 'hooks' ) );
			}
		}
	}
	
	/**
	 * Iterates through array of provided methods signatures and executes them.
	 *
	 * @param       $object
	 * @param array $methods
	 */
	protected function run_initializing_methods_on_object( $object, array $methods ): void {
		foreach ( $methods as $method ) {
			if ( method_exists( $object, $method ) ) {
				$object->$method();
			}
		}
	}
	
	/**
	 * Calls initializing hooks on objects that don't mean to be reused by calling Register. For example you have
	 * object which can do something but its logic invokes only by specific hooks and you don't need to have
	 * instance of this object in Register property. So you simply add this object here and you are free of additional
	 * allocation of new property inside of Register.
	 */
	protected function run_hooks_on_objects(): void {
		$objects = array(
			new Scripts_Loader(),
			new AJAX(),
			new Theme_Setup(),
		);
		
		if ( is_admin() ) {
		
		}
		
		foreach ( $objects as $object ) {
			$this->run_initializing_methods_on_object( $object, array( 'init', 'hooks' ) );
		}
	}
	
	/**
	 * Attaches debug method for frontend and admin side. You can invoke this method in 'init' method.
	 */
	protected function add_debug_method(): void {
		$callback = function () {
			if ( ! wp_doing_ajax() ) {
				$this->test();
			}
		};
		add_action( 'wp_head', $callback );
		add_action( 'admin_init', $callback );
	}
	
	/**
	 * Debug method that usually invokes in the very beginning of the page. You can add your code to this method and
	 * debug it or print output.
	 */
	protected function test(): void {
	
	}
}