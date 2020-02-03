<?php

namespace your\space;

/**
 * Loads classes and other abstraction instances. Each abstraction instance have to have prefix
 * of its abstraction type. Example: if you are loading abstraction instance Header you should name it accordingly to
 * abstraction type:
 *
 * class: class-header.php
 * abstract class: abstract-header.php
 * interface: interface-header.php
 * trait: trait-header.php
 */
class Autoloader {
	protected $class_name;
	protected $classes_map = array();

	function __construct() {
		/**
		 * class mapper can be found in github repository:
		 * https://github.com/alex-2077/php-classes-mapper
		 */
		if ( file_exists( __DIR__ . '/exported-map.php' ) ) {
			$this->classes_map = include 'exported-map.php';
		}
	}

	function load_from_custom_directories( $class_name ) {
		if ( isset( $this->classes_map[ $class_name ] ) && file_exists( $this->classes_map[ $class_name ] ) ) {
			require_once $this->classes_map[ $class_name ];

			return;
		}

		$this->class_name = $this->get_processed_class_name( $class_name );

		$found_file = $this->search_file();

		if ( file_exists( $found_file ) ) {
			require_once $found_file;
		}
	}

	protected function search_file() {
		foreach ( new \RecursiveIteratorIterator( $this->get_directory_iterator() ) as $file ) {
			/**
			 * @var \RecursiveDirectoryIterator $file
			 */
			if ( $file->isFile() && $this->is_needed_file( $file->getFilename() ) ) {
				return $file->getPathName();
			}
		}

		return '';
	}

	protected function get_directory_iterator() {
		$excluded_dir = __DIR__ . '/libs';
		$dir          = new \RecursiveDirectoryIterator( __DIR__ . '/..', \RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator     = new \RecursiveCallbackFilterIterator( $dir, function ( $current, $key, $iterator ) use ( $excluded_dir ) {
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

		return $iterator;
	}

	protected function get_processed_class_name( $class_name ) {
		$class_name = $this->trim_name_space( $class_name );
		$class_name = $this->make_dashed_lowercase_class_name( $class_name );

		return $class_name;
	}

	protected function trim_name_space( $class_name ) {
		if ( strpos( $class_name, '\\' ) ) {
			$class_name = preg_split( '/\\\/', $class_name, null, PREG_SPLIT_NO_EMPTY );
			$class_name = array_pop( $class_name );
		}

		return $class_name;
	}

	protected function make_dashed_lowercase_class_name( $class_name ) {
		return strtolower( str_replace( '_', '-', $class_name ) );
	}

	protected function is_needed_file( $current_file_name ) {
		$file_names = array(
			$this->get_file_class_name( $this->class_name ),
			$this->get_file_abstract_class_name( $this->class_name ),
			$this->get_file_interface_name( $this->class_name ),
			$this->get_file_trait_name( $this->class_name )
		);

		return in_array( $current_file_name, $file_names );
	}

	protected function get_file_class_name( $class_name ) {
		return 'class-' . $class_name . '.php';
	}

	protected function get_file_abstract_class_name( $class_name ) {
		return 'abstract-' . $class_name . '.php';
	}

	protected function get_file_interface_name( $interface_name ) {
		return 'interface-' . $interface_name . '.php';
	}

	protected function get_file_trait_name( $trait_name ) {
		return 'trait-' . $trait_name . '.php';
	}
}

$autoloader = new Autoloader();
spl_autoload_register( array( $autoloader, 'load_from_custom_directories' ) );