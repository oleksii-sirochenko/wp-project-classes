<?php


namespace your\space;

/**
 * Caches data with a help of WP Transients API.
 */
class Transient_Cache {
	
	/**
	 * Expiration time in seconds.
	 *
	 * @var int
	 */
	protected $expiration = DAY_IN_SECONDS * 7;
	
	/**
	 * Already retrieved cache data form transients.
	 *
	 * @var array
	 */
	protected $cache = array();
	
	/**
	 * Option key where all the cache ids are stored.
	 *
	 * @var string
	 */
	protected $option_key = 'transient_cache_ids';
	
	function __construct() {
	
	}
	
	/**
	 * Attaches methods to hooks.
	 */
	function hooks() {
		//W3 TOTAL CACHE hook (plugin must be installed), use this or attach to your custom action
		add_action( 'w3tc_flush_all', array( $this, 'delete_transient_on_w3tc_flush' ) );
	}
	
	/**
	 * Returns cache by its ID. Returns from property or get from transient getter function.
	 *
	 * @param string|number $cache_id
	 *
	 * @return mixed
	 */
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
	
	/**
	 * Sets cache by transient, also saves data to property and saves cache_id for later.
	 *
	 * @param  string|number $cache_id
	 * @param  mixed         $data
	 * @param  int           $expiration in seconds. Do not provide anything to use predefined expiration time.
	 */
	function set_cache( $cache_id, $data, $expiration = 0 ) {
		$this->cache[ $cache_id ] = $data;
		if ( empty( $expiration ) ) {
			$expiration = $this->expiration;
		}
		
		set_transient( $cache_id, $data, $expiration );
		
		$cache_ids = get_option( $this->option_key, array() );
		if ( ! in_array( $cache_id, $cache_ids ) ) {
			$cache_ids[] = $cache_id;
			update_option( $this->option_key, $cache_ids );
		}
	}
	
	/**
	 * Deletes cache from transient and deletes cache id from option.
	 *
	 * @param $cache_id
	 */
	function delete_cache( $cache_id ) {
		unset( $this->cache[ $cache_id ] );
		delete_transient( $cache_id );
		
		$cache_ids = get_option( $this->option_key, array() );
		$cache_ids = array_filter( $cache_ids, function ( $current_cache_id ) use ( $cache_id ) {
			return $current_cache_id !== $cache_id;
		} );
		update_option( $this->option_key, $cache_ids );
	}
	
	/**
	 * Flushes all caches on W3TC action.
	 */
	function delete_transient_on_w3tc_flush() {
		if ( isset( $_GET['w3tc_flush_all'] ) ) {
			$this->flush_all_caches();
		}
	}
	
	/**
	 * Flushes all caches.
	 */
	function flush_all_caches() {
		$cache_ids = get_option( $this->option_key, array() );
		foreach ( $cache_ids as $cache_id ) {
			delete_transient( $cache_id );
		}
		update_option( $this->option_key, array() );
	}
	
	/**
	 * Caching method that accepts cache id and callable instance which assumed to be rendered of HTML template.
	 * Checks for saved cache and returns if it is present or invokes callable and buffers it output to save and return
	 * it HTML template as string.
	 *
	 * @param $cache_id
	 * @param $callback
	 *
	 * @return mixed
	 */
	function cache_result_tmpl_render_callback( $cache_id, $callback ) {
		$cache = $this->get_cache( $cache_id );
		if ( ! empty( $cache ) && ! WP_DEBUG ) {
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