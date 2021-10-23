<?php

namespace your\space;

/**
 * Validates provided data and returns the result or array with not valid fields.
 */
class Data_Validator {
	
	/**
	 * Contains config for data validation. This is array of arrays. Each array item represents configuration for a
	 * single data item.
	 *
	 * array(
	 *      'error_messages' => array(
	 *          'required'     => string Error message when field is not set,
	 *          'not_empty'    => string Error message when field is empty.
	 *          'invalid_type' => string Error message when field has not valid data type. Must contain 2 %s
	 *                            placeholders for type provided and for type awaited.
	 *                            'Provided: %s; Awaited: %s'
	 *      ),
	 *      'fields' => array(
	 *          'field_key' => array(
	 *              'type'              => string | int | float | object | null | bool | array | assoc_array |
	 *                                      indexed_array or array of combinations,
	 *              'required'          => bool Is value required,
	 *              'not_empty'         => bool Is value not empty.
	 *              'default'           => mixed default value,
	 *              'error_messages' => array(
	 *                  'required'     => 'This field is required.',
	 *                  'not_empty'    => 'This field can not be empty.'
	 *                  'invalid_type' => 'Provided: %s; Awaited: %s'
	 *              )
	 *              'sanitize_callbacks' => array(
	 *                  callable items which receive values
	 *              ),
	 *              'validate_callbacks' => array(
	 *                    array(
	 *                      'callback'      => callable
	 *                      'error_message' => string
	 *                  )
	 *              ),
	 *          ),
	 *      ),
	 * )
	 *
	 * @var array
	 */
	protected $config;
	
	/**
	 * Data provided for sanitizing and validation.
	 *
	 * @var mixed
	 */
	protected $initial_data;
	
	/**
	 * Data_Validator constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		$this->config = $config;
		$this->set_error_messages();
	}
	
	/**
	 * Sets error messages. Combines provided with default messages.
	 */
	protected function set_error_messages() {
		$default_error_messages = array(
			'required'     => 'This field is required.',
			'not_empty'    => 'This field can not be empty.',
			'invalid_type' => 'Has invalid data type. Provided: %s; Awaited: %s.',
		);
		
		if ( isset( $this->config['error_messages'] ) ) {
			$this->config['error_messages'] = array_merge( $default_error_messages, $this->config['error_messages'] );
		} else {
			$this->config['error_messages'] = $default_error_messages;
		}
	}
	
	/**
	 * Processes data. Checks for data type, required fields for array, object, sanitizes and validates them.
	 *
	 * @param array $data
	 * @param array $fields_config
	 *
	 * @return array
	 */
	public function process_data( array $data, array $fields_config ) {
		if ( ! isset( $this->initial_data ) ) {
			$this->initial_data = $data;
		}
		
		$result = array(
			'errors' => array(),
			'data'   => array(),
		);
		
		foreach ( $fields_config as $key => $cfg_item ) {
			$this->process_single_data_item( $result, $data, $cfg_item, $key );
		}
		
		return $result;
	}
	
	/**
	 * Processes single data item.
	 *
	 * @param array  $result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 */
	protected function process_single_data_item( array &$result, array $data, array $cfg_item, $key ) {
		$this->check_cfg_item( $cfg_item );
		$this->set_default_value( $data, $cfg_item, $key );
		
		if ( empty( $this->validate_required( $result, $data, $cfg_item, $key ) ) || ! isset( $data[ $key ] ) ) {
			return;
		}
		
		if ( empty( $this->validate_not_empty( $result, $data, $cfg_item, $key ) ) ) {
			return;
		}
		
		if ( empty( $this->validate_data_type( $result, $data, $cfg_item, $key ) ) ) {
			return;
		}
		
		if ( $cfg_item['type'] === 'assoc_array' ) {
			$this->process_assoc_array( $result, $data, $cfg_item, $key );
		} elseif ( $cfg_item['type'] === 'indexed_array' ) {
			$this->process_indexed_array( $result, $data, $cfg_item, $key );
		} else {
			$this->sanitize_value( $result, $data, $cfg_item, $key );
			$this->validate_value( $result, $data, $cfg_item, $key );
		}
	}
	
	/**
	 * Checks cfg_item for all required properties and throws error if some is not specified. In the end returns bool.
	 *
	 * @param array $cfg_item
	 *
	 * @return bool
	 */
	protected function check_cfg_item( $cfg_item ) {
		$error_message = '"%s" key is not in field configuration item.';
		
		if ( ! isset( $cfg_item['type'] ) ) {
			throw new \InvalidArgumentException( sprintf( $error_message, 'type' ) );
		}
		
		$keys = array(
			'type',
			'required',
			'not_empty',
		);
		
		if ( in_array( $cfg_item['type'], array( 'assoc_array', 'indexed_array' ) ) ) {
			$keys[] = 'fields';
		} else {
			$keys[] = 'sanitize_callbacks';
			$keys[] = 'validate_callbacks';
		}
		
		foreach ( $keys as $key ) {
			if ( ! isset( $cfg_item[ $key ] ) ) {
				throw new \InvalidArgumentException( sprintf( $error_message, $key ) );
			}
		}
	}
	
	/**
	 * Sets default if key is not present or value is empty and default value is set for current field.
	 *
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 */
	protected function set_default_value( array &$data, array $cfg_item, $key ) {
		if ( ! isset( $data[ $key ] ) || empty( $data[ $key ] ) && isset( $cfg_item['default'] ) ) {
			$data[ $key ] = $cfg_item['default'];
		}
	}
	
	/**
	 * Processes sub result when data item is associative array.
	 *
	 * @param array  $result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 */
	protected function process_assoc_array( array &$result, array $data, array $cfg_item, $key ) {
		if ( ! is_array( $data[ $key ] ) ) {
			$result['errors'][ $key ] = sprintf(
				$this->config['error_messages']['invalid_type'],
				$this->get_type( $data[ $key ] ),
				$cfg_item['type']
			);
			
			return;
		}
		
		$sub_result = $this->process_data( $data[ $key ], $cfg_item['fields'] );
		
		if ( ! empty( $sub_result['errors'] ) ) {
			foreach ( $sub_result['errors'] as $error_key => $error_message ) {
				$result['errors'][ $key . '[\'' . $error_key . '\']' ] = $error_message;
			}
		}
		
		foreach ( $sub_result['data'] as $sub_result_key => $sub_result_data ) {
			$result['data'][ $key ][ $sub_result_key ] = $sub_result_data;
		}
	}
	
	/**
	 * Processes sub result when data item is indexed array.
	 *
	 * @param array  $result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 */
	protected function process_indexed_array( array &$result, array $data, array $cfg_item, $key ) {
		foreach ( $data[ $key ] as $index => $item_value ) {
			$sub_result = array(
				'errors' => array(),
				'data'   => array(),
			);
			
			$this->process_single_data_item( $sub_result, array( $item_value ), $cfg_item['fields'][0], 0 );
			
			if ( ! empty( $sub_result['errors'] ) ) {
				foreach ( $sub_result['errors'] as $error_message ) {
					$result['errors'][ $key . '[\'' . $index . '\']' ] = $error_message;
				}
			}
			foreach ( $sub_result['data'] as $sub_result_data ) {
				$result['data'][ $key ][ $index ] = $sub_result_data;
			}
		}
	}
	
	/**
	 * Validates if the field is required and it is present or not.
	 *
	 * @param array &$result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 *
	 * @return bool true if validation passed or false if not.
	 */
	protected function validate_required( array &$result, array $data, array $cfg_item, $key ) {
		if ( $cfg_item['required'] && ! isset( $data[ $key ] ) ) {
			if ( isset( $cfg_item['error_messages'] ) && isset( $cfg_item['error_messages']['required'] ) ) {
				$result['errors'][ $key ] = $cfg_item['error_messages']['required'];
			} else {
				$result['errors'][ $key ] = $this->config['error_messages']['required'];
			}
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Validates if the field is not empty.
	 *
	 * @param array &$result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 *
	 * @return bool true if validation passed or false if not.
	 */
	protected function validate_not_empty( array &$result, array $data, array $cfg_item, $key ) {
		if ( $cfg_item['not_empty'] && ( ! isset( $data[ $key ] ) || empty( $data[ $key ] ) ) ) {
			if ( isset( $cfg_item['error_messages'] ) && isset( $cfg_item['error_messages']['not_empty'] ) ) {
				$result['errors'][ $key ] = $cfg_item['error_messages']['not_empty'];
			} else {
				$result['errors'][ $key ] = $this->config['error_messages']['not_empty'];
			}
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Validates if the field has awaited data type.
	 *
	 * @param array  &$result
	 * @param array   $data
	 * @param array   $cfg_item
	 * @param string  $key
	 *
	 * @return bool true if validation passed or false if not.
	 */
	protected function validate_data_type( array &$result, array $data, array $cfg_item, $key ) {
		if ( empty( $this->is_valid_data_type( $cfg_item['type'], $data[ $key ] ) ) ) {
			if ( is_array( $cfg_item['type'] ) ) {
				$type = implode( ', ', $cfg_item['type'] );
			} else {
				$type = $cfg_item['type'];
			}
			
			if ( isset( $cfg_item['error_messages']['invalid_type'] ) ) {
				$error_message = $cfg_item['error_messages']['invalid_type'];
			} else {
				$error_message = $this->config['error_messages']['invalid_type'];
			}
			
			$result['errors'][ $key ] = sprintf( $error_message, $this->get_type( $data[ $key ] ), $type );
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Sanitizes value with callbacks.
	 *
	 * @param array &$result
	 * @param array  $data
	 * @param array  $cfg_item
	 * @param string $key
	 */
	protected function sanitize_value( array &$result, array $data, array $cfg_item, $key ) {
		foreach ( $cfg_item['sanitize_callbacks'] as $sanitize_callback ) {
			if ( ! is_callable( $sanitize_callback ) ) {
				throw new \InvalidArgumentException(
					'Sanitization callback is not callable: ' . json_encode( $sanitize_callback )
				);
			}
			$result['data'][ $key ] = call_user_func( $sanitize_callback, $data[ $key ], $key, $this->initial_data );
		}
	}
	
	/**
	 * Validates value with callbacks.
	 *
	 * @param array  &$result
	 * @param array   $data
	 * @param array   $cfg_item
	 * @param string  $key
	 */
	protected function validate_value( array &$result, array $data, array $cfg_item, $key ) {
		foreach ( $cfg_item['validate_callbacks'] as $validate_callback ) {
			if ( ! isset( $validate_callback['callback'] ) || ! is_callable( $validate_callback['callback'] ) ) {
				throw new \InvalidArgumentException(
					'Validation callback is not callable: ' . json_encode( $validate_callback )
				);
			}
			
			if ( ! isset( $validate_callback['error_message'] ) || empty( $validate_callback['error_message'] ) ) {
				throw new \InvalidArgumentException(
					'There is no error message for validation callback: ' . json_encode( $validate_callback )
				);
			}
			
			if ( is_callable( $validate_callback['callback'] ) ) {
				if ( empty( call_user_func( $validate_callback['callback'], $data[ $key ], $key, $this->initial_data ) ) ) {
					$result['errors'][ $key ] = $validate_callback['error_message'];
				}
			}
		}
	}
	
	/**
	 * Checks data variable type.
	 *
	 * @param string|array $type
	 * @param mixed        $value
	 *
	 * @return bool
	 */
	protected function is_valid_data_type( $type, $value ) {
		if ( is_string( $type ) ) {
			$types = array( $type );
		} elseif ( is_array( $type ) ) {
			$types = $type;
		} elseif ( $type === 'assoc_array' || $type === 'indexed_array' ) {
			$types = array( 'array' );
		} else {
			return false;
		}
		
		$supported_types = array(
			'string',
			'int',
			'float',
			'array',
			'object',
			'null',
			'bool'
		);
		
		foreach ( $types as $type ) {
			// Return true if type is unknown.
			if ( ! in_array( $type, $supported_types ) ) {
				return true;
			}
			
			$resolved_type = $this->get_type( $value );
			
			if ( $resolved_type === $type ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns normalized version of type of the provided value.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	protected function get_type( $value ) {
		$type = gettype( $value );
		
		// Normalizes gettype result.
		if ( $type === 'boolean' ) {
			$type = 'bool';
		} elseif ( $type === 'integer' ) {
			$type = 'int';
		} elseif ( $type === 'double' ) {
			$type = 'float';
		} elseif ( $type === 'NULL' ) {
			$type = 'null';
		}
		
		return $type;
	}
}