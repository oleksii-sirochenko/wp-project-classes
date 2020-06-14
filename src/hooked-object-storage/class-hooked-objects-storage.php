<?php


namespace your\space;

/**
 * simple storage for objects that were attached to filters. With the help of this storage you can always get your
 * object from it and remove_filter or remove_action
 */
class Hooked_Objects_Storage {
    protected $objects = array();
    
    function __construct() {
        
    }
    
    function set_object( $obj ) {
        $this->objects[ get_class( $obj ) ][ spl_object_hash( $obj ) ] = $obj;
    }
    
    /**
     * @param string $signature - signature prefixed with namespace;
     *
     * @return array of objects
     */
    function get_objects( $signature ) {
        if ( isset( $this->objects[ $signature ] ) && isset( $this->objects[ $signature ] ) ) {
            return $this->objects[ $signature ];
        }
        
        return array();
    }
    
    function delete_objects( $signature ) {
        unset( $this->objects[ $signature ] );
    }
    
    /**
     * @param string $signature
     * @param string $hash - result of spl_object_hash applied for object
     *
     * @return object|null
     */
    function get_object( $signature, $hash ) {
        if ( isset( $this->objects[ $signature ] ) && isset( $this->objects[ $signature ][ $hash ] ) ) {
            return $this->objects[ $signature ][ $hash ];
        }
        
        return null;
    }
    
    function delete_object( $signature, $hash ) {
        unset( $this->objects[ $signature ][ $hash ] );
    }
}