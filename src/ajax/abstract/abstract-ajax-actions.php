<?php

namespace your\space;

/**
 * Abstract instance of default AJAX actions functionality.
 */
abstract class AJAX_Actions {
	/**
	 * Must return array of arrays. Each array item is consists of action key with key and function key with callable.
	 * It can have 'logged' key that will turn actions to be attached for logged in users or not. If 'logged' key is not
	 * specified AJAX action attaches to both types of users.
	 *
	 * @return array
	 */
	public abstract function get_actions();
	
	/**
	 * Should return associative array of scripts data.
	 *
	 * @return array
	 */
	public function get_scripts_data() {
		return array();
	}
}