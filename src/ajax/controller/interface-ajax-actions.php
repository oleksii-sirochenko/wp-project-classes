<?php

namespace your\space;

/**
 * Interface for AJAX actions functionality.
 */
interface AJAX_Actions {
	/**
	 * Must return array of arrays. Each array item is consists of action key with key and function key with callable.
	 * It can have 'logged' key that will turn actions to be attached for logged in users or not. If 'logged' key is not
	 * specified AJAX action attaches to both types of users.
	 *
	 * @return array
	 */
	public function get_actions(): array;
}