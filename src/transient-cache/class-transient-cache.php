<?php


namespace your\space;


class Transient_Cache {
    protected $expiration = DAY_IN_SECONDS * 7;
    protected $cache = array();
    protected $option_key = 'transient_cache_ids';
    
    function __construct() {
        
    }
    
    function hooks() {
        //W3 TOTAL CACHE hook (plugin must be installed), use this or attach to your custom action
        add_action( 'w3tc_redirect', array( $this, 'delete_transient_on_w3tc_flush' ) );
    }
    
    function get_cache( $cache_id ) {
        if ( isset( $this->cache[ $cache_id ] ) && ! empty( $this->cache[ $cache_id ] ) ) {
            return $this->cache[ $cache_id ];
        }
        
        $cache = get_transient( $cache_id );
        if ( ! empty( $cache ) ) {
            $this->cache[ $cache_id ] = $cache;
        }
        
        return $cache;
    }
    
    function set_cache( $cache_id, $data ) {
        $this->cache[ $cache_id ] = $data;
        set_transient( $cache_id, $data, $this->expiration );
    }
    
    function delete_cache( $cache_id ) {
        unset( $this->cache[ $cache_id ] );
        delete_transient( $cache_id );
    }
    
    function register_cache_id( $cache_id ) {
        $cache_ids = get_option( $this->option_key, array() );
        if ( ! in_array( $cache_id, $cache_ids ) ) {
            $cache_ids[] = $cache_id;
            update_option( $this->option_key, $cache_ids );
        }
    }
    
    function delete_transient_on_w3tc_flush() {
        if ( isset( $_GET['w3tc_flush_all'] ) ) {
            $cache_ids = get_option( $this->option_key, array() );
            foreach ( $cache_ids as $cache_id ) {
                delete_transient( $cache_id );
            }
            update_option( $this->option_key, array() );
        }
    }
    
    function cache_result_tmpl_render_callback( $cache_id, $callback ) {
        $cache = $this->get_cache( $cache_id );
        if ( ! empty( $cache ) && ! Reg::inst()->is_localhost() ) {
            return $cache;
        }
        if ( ! is_callable( $callback ) ) {
            return false;
        }
        ob_start();
        call_user_func( $callback );
        $result = ob_get_clean();
        
        if ( ! empty( $result ) ) {
            $this->set_cache( $cache_id, $result );
        }
        
        return $result;
    }
}