<?php

namespace your\space;

/**
 * Registry is a final singleton class that controls classes
 */
final class Reg {
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
    
    /**
     * AJAX controller. Attaches all AJAX actions with their handlers.
     *
     * @var AJAX
     */
    public $ajax;
    
    protected function __construct() {
        $this->tmpl = new Template_Loader( $this->get_theme_directory() );
        $this->init_ajax();
        
        if ( is_admin() ) {
        
        }
    }
    
    /**
     * Method that returns singleton instance.
     *
     * @return Reg
     */
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
    }
    
    /**
     * Registry invokes certain methods on each object that has this methods. Usually other objects
     * want to attach their methods to specific hooks or run initialization function after creation.
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
    
    /**
     * Iterates through array of provided methods signatures and executes them.
     *
     * @param       $object
     * @param array $methods
     */
    protected function run_initializing_methods_on_object( $object, array $methods ) {
        foreach ( $methods as $method ) {
            if ( method_exists( $object, $method ) ) {
                $object->$method();
            }
        }
    }
    
    /**
     * Calls initializing hooks on objects that don't mean to be reused by calling Register. For example you have
     * object with that can do something but its logic invokes only by specific hooks and you don't need to have
     * instance of this object in Register property. So you simply add this object here and you are free of additional
     * allocation of new property inside of Register.
     */
    protected function run_hooks_on_objects() {
        $objects = array(
            new Scripts_Loader(),
        );
        
        if ( is_admin() ) {
        
        }
        
        foreach ( $objects as $object ) {
            $this->run_initializing_methods_on_object( $object, array( 'hooks' ) );
        }
    }
    
    /**
     * Initializes AJAX logic. In this method you can create AJAX controller and add object with ajax actions to it.
     */
    protected function init_ajax() {
        $this->ajax = new AJAX();
        $this->ajax->add_ajax_actions( new Front_Page_AJAX_Actions() );
    }
    
    /**
     * Checks is site running under development environment.
     *
     * @return bool
     */
    public function is_localhost() {
        return getenv( 'is_localhost' ) == 'true';
    }
    
    /**
     * Attaches debug method for frontend and admin side. You can invoke this method in 'init' method.
     */
    protected function add_debug_method() {
        $callback = function () {
            if ( ! wp_doing_ajax() ) {
                $this->test();
            }
        };
        add_action( 'wp_head', $callback );
        add_action( 'admin_init', $callback );
    }
    
    /**
     * Debug method that usully invokes in the very beginning of the page. You can add your code to this method and
     * debug it or print output.
     */
    protected function test() {
    
    }
}