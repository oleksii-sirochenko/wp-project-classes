<?php

namespace your\space;

/**
 * Shortcodes controller. Main responsibility is to collect and initialize shortcodes.
 */
class Shortcodes {
	/**
	 * Collects shortcodes. Key is a class name namespace and value is object.
	 *
	 * @var array
	 */
	protected $shortcodes;
	
	public function __construct() {
	
	}
	
	/**
	 * Initializes logic.
	 */
	public function init() {
		$shortcodes = array(
			'your\space\Custom_Shortcode',
		);
		
		foreach ( $shortcodes as $class_name ) {
			/**
			 * @var Shortcode $shortcode
			 */
			$shortcode = new $class_name();
			add_shortcode( $shortcode->get_shortcode_name(), array( $shortcode, 'do_shortcode' ) );
			$this->shortcodes[ $class_name ] = $shortcode;
		}
	}
}