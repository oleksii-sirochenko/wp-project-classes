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
    
    protected function hooks() {
        register_activation_hook( __FILE__, array( $this, 'register_activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'register_deactivation' ) );
    }
    
    function register_activation() {
        Reg::inst()->register_activation_hooks();
    }
    
    function register_deactivation() {
        Reg::inst()->register_deactivation_hooks();
    }
    
    protected function init() {
        $this->reg = Reg::inst();
    }
    
    static function plugin_url() {
        return plugin_dir_url( __FILE__ );
    }
}

Plugin_Starter::inst();