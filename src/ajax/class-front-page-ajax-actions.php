<?php


namespace your\space;


class Front_Page_AJAX_Actions extends AJAX_Actions {
    protected function get_actions_sets_array() {
        return array_merge( array(),
            $this->front_page_actions()
        );
    }
    
    protected function front_page_actions() {
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