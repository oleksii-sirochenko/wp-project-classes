<?php

namespace your\space;

/**
 * General logic for shortcode classes.
 */
abstract class Shortcode {
	/**
	 * Returns shortcode name.
	 *
	 * @return string
	 */
	public abstract function get_shortcode_name();
	
	/**
	 * Invokes main logic for current class.
	 *
	 * @param array  $atts
	 * @param string $content
	 * @param string $tag
	 *
	 * @return string HTML template.
	 */
	public abstract function do_shortcode( $atts, $content, $tag );
	
	/**
	 * Returns page url for automatically created page that contains current shortcode.
	 *
	 * @return string
	 */
	public function get_attached_page_url() {
		$required_pages_creator = new Required_Pages();
		
		return get_permalink( $required_pages_creator->get_page_id_by_shortcode( $this->get_shortcode_name() ) );
	}
}