<?php

namespace your\space;

/**
 * Collects AJAX actions and returns to its attached controller.
 */
class Front_Page_AJAX_Actions extends AJAX_Actions {
	public function get_actions() {
		return array(
			array(
				'action'   => 'get_home_url',
				'function' => function () {
					AJAX::validate_request();
					wp_send_json( AJAX::get_success_response( home_url() ) );
				},
			),
		);
	}
}