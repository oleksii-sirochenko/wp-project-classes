<?php
$config = array(
	'error_messages' => array(
		'required'     => 'Error message when field is not set',
		'not_empty'    => 'Error message when field is empty',
		'invalid_type' => 'Provided: %s; Awaited: %s'
	),
);

$validator = new \your\space\Data_Validator( $config );

$fields_cfg = array(
	'key1'    => array(
		'type'               => 'string',
		'required'           => false,
		'not_empty'          => false,
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
	'date'    => array(
		'type'      => 'assoc_array',
		'required'  => true,
		'not_empty' => false,
		'fields'    => array(
			'year'  => array(
				'type'               => 'int',
				'required'           => true,
				'not_empty'          => false,
				'sanitize_callbacks' => array(
					'intval',
				),
				'validate_callbacks' => array(
					array(
						'callback'      => '\your\space\is_not_empty',
						'error_message' => 'This field can not be empty.',
					),
				),
			),
			'month' => array(
				'type'               => 'int',
				'required'           => true,
				'not_empty'          => false,
				'sanitize_callbacks' => array(
					'intval',
				),
				'validate_callbacks' => array(
					array(
						'callback'      => '\your\space\is_not_empty',
						'error_message' => 'This field can not be empty.',
					),
				),
			),
			'day'   => array(
				'type'               => 'int',
				'required'           => true,
				'not_empty'          => false,
				'sanitize_callbacks' => array(
					'intval',
				),
				'validate_callbacks' => array(
					array(
						'callback'      => '\your\space\is_not_empty',
						'error_message' => 'This field can not be empty.',
					),
				),
			),
		)
	),
	'key2'    => array(
		'type'               => 'int',
		'required'           => false,
		'not_empty'          => false,
		'sanitize_callbacks' => array(
			'sanitize_text_field',
		),
		'validate_callbacks' => array(
			array(
				'callback'      => '\your\space\is_not_empty',
				'error_message' => 'This field can not be empty.',
			)
		),
	),
	'key3'    => array(
		'type'               => array( 'float', 'string' ),
		'required'           => false,
		'not_empty'          => false,
		'sanitize_callbacks' => array(),
		'validate_callbacks' => array(),
	),
	'key4'    => array(
		'type'               => array(
			'int',
			'float',
		),
		'required'           => false,
		'not_empty'          => false,
		'sanitize_callbacks' => array(),
		'validate_callbacks' => array(),
	),
	'numbers' => array(
		'type'      => 'indexed_array',
		'required'  => true,
		'not_empty' => false,
		'fields'    => array(
			array(
				'type'               => array( 'string', 'int' ),
				'required'           => true,
				'not_empty'          => false,
				'sanitize_callbacks' => array(
					'intval',
				),
				'validate_callbacks' => array(
					array(
						'callback'      => '\your\space\is_not_empty',
						'error_message' => 'This field can not be empty.',
					),
				),
			),
		)
	),
);

$data   = array(
	'date'    => array(
		'year'  => 2021,
		'month' => 10,
		'day'   => 3,
	),
	'key1'    => 'hello',
	'key2'    => 123,
	'key3'    => 123,
	'key4'    => 123,
	'numbers' => array( '1', '2', '3', 4, 5 ),
);
$result = $validator->process_data( $data, $fields_cfg );