<?php


namespace your\space;


abstract class AJAX_Actions {
	function get_actions() {
		$actions_sets = array( $this->get_nonce_actions() );

		foreach ( $this->get_actions_sets_array() as $actions_set ) {
			$actions_sets[] = $actions_set;
		}

		$actions = array();
		foreach ( $actions_sets as $actions_set ) {
			$actions += $actions_set;
		}

		return $actions;
	}

	/**
	 * default actions set
	 * @return array
	 */
	protected function get_nonce_actions() {
		return array(
			'get_nonce' => array(
				'action'   => 'get_nonce',
				'function' => function () {
					wp_send_json( AJAX::get_success_response( null, null ) );
				},
			),
		);
	}


	/**
	 * should return array of actions set
	 * @return array
	 */
	abstract protected function get_actions_sets_array();
}