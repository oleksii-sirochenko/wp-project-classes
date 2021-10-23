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
				'args'     =>  array(
					'key1' => array(
						'type'               => 'string',
						'required'           => true,
						'not_empty'          => true,
						'error_messages'     => array(
							'required'  => 'Custom message. This field is required.',
							'not_empty' => 'Custom message. This field can not be empty.',
						),
						'sanitize_callbacks' => array(
							'sanitize_text_field',
						),
						'validate_callbacks' => array(
							array(
								'callback'      => '\your\space\is_not_empty',
								'error_message' => 'This field can not be empty.',
							),
						),
					),
					'key2' => array(
						'type'               => 'string',
						'required'           => true,
						'not_empty'          => true,
						'default'            => 'world',
						'error_messages'     => array(
							'required'  => 'Custom message. This field is required.',
							'not_empty' => 'Custom message. This field can not be empty.',
						),
						'sanitize_callbacks' => array(
							'sanitize_text_field',
						),
						'validate_callbacks' => array(
							array(
								'callback'      => '\your\space\is_not_empty',
								'error_message' => 'This field can not be empty.',
							),
						),
					),
				),
			),
		);
	}
}