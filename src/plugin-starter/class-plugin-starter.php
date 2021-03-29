<?php

/**
 * Plugin Name: Plugin
 * Plugin URI:  https://example.com/plugins/the-basics/
 * Description:
 * Version:     20991212
 * Author:      WordPress.org
 * Author URI:  https://author.example.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: your_domain
 * Domain Path: /languages
 */

namespace your\space;

final class Plugin_Starter {
	const PATH = __DIR__;
	protected static $instance;
	protected $reg;
	
	protected function __construct() {
	
	}
	
	/**
	 * Singleton method. Ensures that plugin code initializes only once.
	 */
	static function inst() {
		if ( isset( self::$instance ) ) {
			return self::$instance;
		} else {
			self::$instance = new self();
			
			require_once self::PATH . '/includes/constants/constants.php';
			require_once self::PATH . '/includes/classes/autoloader/class-autoloader.php';
			
			self::$instance->init();
			self::$instance->hooks();
			
			return self::$instance;
		}
	}
	
	/**
	 * Makes initialization of the main logic.
	 */
	protected function hooks() {
		register_activation_hook( __FILE__, array( $this, 'register_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'register_deactivation' ) );
	}
	
	/**
	 * Attaches activation and deactivation plugin hooks.
	 */
	function register_activation() {
		Reg::inst()->register_activation_hooks();
	}
	
	/**
	 * Invokes method during plugin activation.
	 */
	function register_deactivation() {
		Reg::inst()->register_deactivation_hooks();
	}
	
	/**
	 * Invokes method during plugin deactivation.
	 */
	protected function init() {
		$this->reg = Reg::inst();
	}
	
	/**
	 * Returns root path for plugin.
	 *
	 * @return string
	 */
	static function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}
}

Plugin_Starter::inst();