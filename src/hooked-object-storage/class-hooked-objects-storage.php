<?php

namespace your\space;

/**
 * Simple storage for objects that were attached to filters. With the help of this storage you can always get your
 * object from it and remove_filter or remove_action.
 */
class Hooked_Objects_Storage {
	/**
	 * @var array storage.
	 */
	protected $objects = array();
	
	function __construct() {
	
	}
	
	/**
	 * Sets object to storage. Uses object class signature and result of spl_object_hash as keys.
	 *
	 * @param $obj
	 *
	 * @return string
	 */
	public function set_object( $obj ) {
		$hash = spl_object_hash( $obj );
		
		$this->objects[ get_class( $obj ) ][ $hash ] = $obj;
		
		return $hash;
	}
	
	/**
	 * @param string $signature - signature prefixed with namespace;
	 *
	 * @return array of objects
	 */
	public function get_objects( $signature ) {
		if ( isset( $this->objects[ $signature ] ) && isset( $this->objects[ $signature ] ) ) {
			return $this->objects[ $signature ];
		}
		
		return array();
	}
	
	/**
	 * Deletes array of objects by its class signature.
	 *
	 * @param $signature
	 */
	public function delete_objects( $signature ) {
		unset( $this->objects[ $signature ] );
	}
	
	/**
	 * Searches for object by provided class signature and its hash or returns the first object by provided signature
	 * if hash is empty.
	 *
	 * @param string $signature
	 * @param string $hash - result of spl_object_hash applied for object
	 *
	 * @return object|null
	 */
	public function get_object( $signature, $hash = '' ) {
		if ( isset( $this->objects[ $signature ] ) && ! empty( $this->objects[ $signature ] ) ) {
			if ( empty( $hash ) ) {
				return array_values( $this->objects[ $signature ] )[0];
			} else {
				if ( isset( $this->objects[ $signature ][ $hash ] ) ) {
					return $this->objects[ $signature ][ $hash ];
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Deletes object by provided class signature and it hash.
	 *
	 * @param $signature
	 * @param $hash
	 */
	public function delete_object( $signature, $hash ) {
		unset( $this->objects[ $signature ][ $hash ] );
	}
}