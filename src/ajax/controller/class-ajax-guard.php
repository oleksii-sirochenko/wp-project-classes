<?php

namespace your\space;

/**
 * Invokes before actual AJAX handler to make sanitizing and validation of provided data. If data is valid then actual
 * registered callback will be invoke, otherwise there will be error response from a guard.
 */
class AJAX_Guard {
	
	/**
	 * Configuration of an AJAX action.
	 *
	 * @var array $action
	 */
	protected $action;
	
	/**
	 * AJAX_Guard constructor.
	 *
	 * @param array $action
	 */
	public function __construct( $action ) {
		$this->action = $action;
	}
	
	/**
	 * Action to be invoked before actual registered callback is invoked. Assures that all required data is provided and
	 * valid.
	 */
	public function guard_action(): void {
		$args = $_REQUEST;
		
		$args     = $this->sanitize( $args );
		$response = $this->validate( $args );
		
		unset( $args['action'] );
		unset( $args['nonce'] );
		
		if ( $response[0] === STATUS_ERROR ) {
			$this->do_response( $response );
		}
		
		$response = call_user_func( $this->action['callback'], $args );
		$this->do_response( $response );
	}
	
	/**
	 * Sanitizes provided data.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	protected function sanitize( array $args ): array {
		foreach ( $this->action['args'] as $key => $arg ) {
			if ( isset( $arg['default'] ) && ! isset( $args[ $key ] ) ) {
				$args[ $key ] = $arg['default'];
			}
			
			if ( ! isset( $arg['sanitize_callback'] ) ||
			     ! is_array( $arg['sanitize_callback'] ) ||
			     empty( $arg['sanitize_callback'] )
			) {
				continue;
			}
			
			foreach ( $arg['sanitize_callback'] as $callback ) {
				if ( ! isset( $args[ $key ] ) ) {
					continue;
				}
				if ( is_callable( $callback ) ) {
					$args[ $key ] = call_user_func( $callback, $args[ $key ] );
				} elseif ( is_array( $callback ) ) {
					foreach ( $callback as $item ) {
						if ( is_callable( $item ) ) {
							$args[ $key ] = call_user_func( $item, $args[ $key ] );
						}
					}
				}
			}
		}
		
		return $args;
	}
	
	/**
	 * Validates provided data.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	protected function validate( array $args ): array {
		$invalid_fields = array();
		
		if ( ! isset( $args['nonce'] ) || ! wp_verify_nonce( $args['nonce'], AJAX_ACTION ) ) {
			$invalid_fields[] = 'nonce';
		}
		
		foreach ( $this->action['args'] as $key => $arg ) {
			if ( isset( $arg['required'] ) && ! isset( $args[ $key ] ) ) {
				$invalid_fields[] = $key;
				continue;
			}
			
			$callback = $arg['validate_callback'];
			
			if ( is_callable( $callback ) ) {
				$this->validate_field( $invalid_fields, $callback, $args, $key );
			} elseif ( is_string( $callback ) && is_callable( __NAMESPACE__ . '\\' . $callback ) ) {
				$this->validate_field( $invalid_fields, __NAMESPACE__ . '\\' . $callback, $args, $key );
			} elseif ( is_array( $callback ) && ! empty( $callback ) ) {
				foreach ( $callback as $item ) {
					if ( is_callable( $item ) ) {
						$this->validate_field( $invalid_fields, $item, $args, $key );
					}
				}
			}
		}
		
		if ( empty( $invalid_fields ) ) {
			$status = STATUS_SUCCESS;
		} else {
			$status = STATUS_ERROR;
			$args   = array(
				'invalid_fields' => $invalid_fields,
			);
		}
		
		return array( $status, $args );
	}
	
	/**
	 * Validates field and marks field as invalid if callback result is not true.
	 *
	 * @param array    $invalid_fields
	 * @param callable $callback
	 * @param array    $args
	 * @param          $key
	 */
	protected function validate_field( array &$invalid_fields, callable $callback, array $args, $key ): void {
		if ( empty( call_user_func( $callback, $args[ $key ], $args, $key ) ) ) {
			$invalid_fields[ $key ] = $args[ $key ];
		}
	}
	
	/**
	 * Responses with JSON. Intelligently checks $response variable. If response is an array and first parameter equals
	 * to status 'success' or 'error' this status is applied and second index of array becomes a payload data.
	 * If nothing is provided or response is not conforming to array(status, payload) it responses with $response
	 * variable as a response payload data and with a status 'success'.
	 *
	 * @param $response
	 */
	protected function do_response( $response ): void {
		$status = STATUS_SUCCESS;
		$data   = $response;
		
		if ( is_array( $response ) && ! empty( $response ) ) {
			if ( in_array( $response[0], array( STATUS_SUCCESS, STATUS_ERROR ) ) ) {
				$status = $response[0];
				if ( isset( $response[1] ) ) {
					$data = $response[1];
				}
			}
		}
		
		$response = $this->get_response_with_status( $status, $data );
		wp_send_json( $response );
	}
	
	/**
	 * Returns AJAX response with status, nonce, custom data and response message.
	 *
	 * @param string $status
	 * @param mixed  $data
	 *
	 * @return array
	 */
	protected static function get_response_with_status( $status, $data = null ): array {
		$response = array(
			'status' => $status,
			'nonce'  => wp_create_nonce( AJAX_ACTION ),
			'data'   => $data,
		);
		
		return $response;
	}
}