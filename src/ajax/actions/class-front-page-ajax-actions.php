<?php

namespace your\space;

/**
 * Collects AJAX actions and returns to its attached controller.
 */
class Front_Page_AJAX_Actions implements AJAX_Actions {
	public function get_actions(): array {
		return array(
			array(
				'action'   => 'get_home_url',
				'callback' => function ( $args ) {
					$response = $args;
					
					return array( STATUS_SUCCESS, $response );
				},
				'args'     => array(
					'hello' => array(
						'default'           => 'hello',
						'required'          => true,
						'sanitize_callback' => array(
							'sanitize_text_field',
						),
						'validate_callback' => array(),
					),
					'world' => array(
						'default'           => 'world',
						'required'          => true,
						'sanitize_callback' => array(
							'sanitize_text_field',
						),
						'validate_callback' => array(),
					)
				),
			
			),
		);
	}
}