<?php

namespace your\space;

/**
 * Loads classes and other abstraction instances. Each abstraction instance has to have a prefix
 * of its abstraction type. Example: if you are loading abstraction instance Header you should name it accordingly to
 * abstraction type:
 *
 * class: class-header.php
 * abstract class: abstract-header.php
 * interface: interface-header.php
 * trait: trait-header.php
 *
 * To optimize usage of autoload, class checks exported-map.php for existence and uses it for autoload purpose. If class
 * recognizes exported-map.php it doesn't use runtime recursive filesystem scan to find class, function, constant, etc.
 * During the development it is convenient to use runtime scan, but for production it is highly recommended
 * to build exported-map.php.
 */
class Autoloader {
    /**
     * Processed class name suitable for filesystem scan.
     *
     * @var string
     */
    protected $class_name;
    
    /**
     * Map of pre parsed abstract instances, function, constants that can be use for auto loading.
     *
     * @var array
     */
    protected $classes_map = array();
    
    /**
     * Main instance of recursive directory iterator that will be iterator over to scan filesystem for desired file
     * path.
     *
     * @var \RecursiveDirectoryIterator
     */
    protected $directory_iterator;
    
    function __construct() {
        /**
         * class mapper can be found in github repository:
         * https://github.com/alex-2077/php-classes-mapper
         */
        if ( file_exists( __DIR__ . '/exported-map.php' ) ) {
            $this->classes_map = include 'exported-map.php';
        } else {
            $this->set_directory_iterator();
        }
    }
    
    /**
     * Tries to find and load abstract instance by checking pre build maps of classes or uses runtime recursive
     * filesystem scan.
     *
     * @param string $class_name
     */
    function load_from_custom_directories( $class_name ) {
        if ( ! empty( $this->classes_map ) ) {
            if ( isset( $this->classes_map[ $class_name ] ) &&
                 ! empty( $file_real_path = realpath( __DIR__ . DIRECTORY_SEPARATOR . $this->classes_map[ $class_name ] ) ) ) {
                require_once $file_real_path;
                
                return;
            }
        } else {
            $this->class_name = $this->get_processed_class_name( $class_name );
            
            $found_file = $this->search_file();
            
            if ( file_exists( $found_file ) ) {
                require_once $found_file;
            }
        }
    }
    
    /**
     * Searches file by its file name.
     *
     * @return string
     */
    protected function search_file() {
        foreach ( new \RecursiveIteratorIterator( $this->directory_iterator ) as $file ) {
            /**
             * @var \RecursiveDirectoryIterator $file
             */
            if ( $file->isFile() && $this->is_needed_file( $file->getFilename() ) ) {
                return $file->getPathName();
            }
        }
        
        return '';
    }
    
    /**
     * Creates recursive directory iterator for further usage and stores it into object property.
     */
    protected function set_directory_iterator() {
        $excluded_dir             = __DIR__ . '/../libs';
        $dir                      = new \RecursiveDirectoryIterator( __DIR__ . '/..', \RecursiveDirectoryIterator::SKIP_DOTS );
        $this->directory_iterator = new \RecursiveCallbackFilterIterator( $dir, function ( $current, $key, $iterator ) use ( $excluded_dir ) {
            /**
             * @var \RecursiveDirectoryIterator $iterator
             */
            if ( strpos( $iterator->getPath(), $excluded_dir ) !== false ) {
                return false;
            }
            
            if ( $iterator->isDir() ) {
                return $iterator->hasChildren();
            }
            
            return true;
        } );
    }
    
    /**
     * Returns processed class name suitable for filesystem checks.
     *
     * @param $class_name
     *
     * @return string
     */
    protected function get_processed_class_name( $class_name ) {
        $class_name = $this->trim_name_space( $class_name );
        $class_name = $this->make_dashed_lowercase_class_name( $class_name );
        
        return $class_name;
    }
    
    /**
     * Trims namespace from class name string.
     *
     * @param string $class_name
     *
     * @return string
     */
    protected function trim_name_space( $class_name ) {
        if ( strpos( $class_name, '\\' ) ) {
            $class_name = preg_split( '/\\\/', $class_name, null, PREG_SPLIT_NO_EMPTY );
            $class_name = array_pop( $class_name );
        }
        
        return $class_name;
    }
    
    /**
     * Normilizes class name to be suitable in filesystem scan.
     *
     * @param string $class_name
     *
     * @return string
     */
    protected function make_dashed_lowercase_class_name( $class_name ) {
        return strtolower( str_replace( '_', '-', $class_name ) );
    }
    
    /**
     * Search provided file name with possible file names.
     *
     * @param string $current_file_name
     *
     * @return bool
     */
    protected function is_needed_file( $current_file_name ) {
        $file_names = array(
            $this->get_file_class_name( $this->class_name ),
            $this->get_file_abstract_class_name( $this->class_name ),
            $this->get_file_interface_name( $this->class_name ),
            $this->get_file_trait_name( $this->class_name )
        );
        
        return in_array( $current_file_name, $file_names );
    }
    
    /**
     * Returns file name of class.
     *
     * @param string $class_name
     *
     * @return string
     */
    protected function get_file_class_name( $class_name ) {
        return 'class-' . $class_name . '.php';
    }
    
    /**
     * Returns file name of abstract class.
     *
     * @param string $class_name
     *
     * @return string
     */
    protected function get_file_abstract_class_name( $class_name ) {
        return 'abstract-' . $class_name . '.php';
    }
    
    /**
     * Returns file name of interface.
     *
     * @param string $interface_name
     *
     * @return string
     */
    protected function get_file_interface_name( $interface_name ) {
        return 'interface-' . $interface_name . '.php';
    }
    
    /**
     * Returns file name of trait.
     *
     * @param string $trait_name
     *
     * @return string
     */
    protected function get_file_trait_name( $trait_name ) {
        return 'trait-' . $trait_name . '.php';
    }
}

$autoloader = new Autoloader();
spl_autoload_register( array( $autoloader, 'load_from_custom_directories' ) );