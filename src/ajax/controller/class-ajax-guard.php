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
		
		if ( ! isset( $args['nonce'] ) || ! wp_verify_nonce( $args['nonce'], AJAX_ACTION ) ) {
			$this->do_response( array(
				STATUS_ERROR,
				array(
					'errors' => array(
						'nonce' => 'nonce is not valid',
					)
				)
			) );
		}
		
		$data_validator = new Data_Validator();
		$result         = $data_validator->process_data( $args, $this->action['args'] );
		
		if ( ! empty( $result['errors'] ) ) {
			$this->do_response( array(
				STATUS_ERROR,
				array(
					'errors' => $result['errors'],
				)
			) );
		}
		
		$response = call_user_func( $this->action['callback'], $result['data'] );
		$this->do_response( $response );
	}
	
	/**
	 * Responses with JSON. Intelligently checks $response variable. If response is an array and first parameter equals
	 * to status 'success' or 'error' this status is applied and second index of array becomes a payload data.
	 * If nothing is provided or response is not conforming to array(status, payload) it responses with $response
	 * variable as a response payload data and with a status 'success'.
	 *
	 * @param $response
	 */
	protected function do_response( $response = null ): void {
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