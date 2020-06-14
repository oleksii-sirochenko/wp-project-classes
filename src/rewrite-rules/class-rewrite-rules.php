<?php


namespace your\space;


class Rewrite_Rules {
    function __construct() {
    
    }
    
    function hooks() {
        add_action( 'init', array( $this, 'add_rewrite_rules' ), 10, 0 );
    }
    
    function add_rewrite_rules() {
        $this->page_rule();
    }
    
    protected function page_rule() {
        add_rewrite_tag( '%var_1%', '([^&]+)' );
        add_rewrite_tag( '%var_2%', '([^&]+)' );
        add_rewrite_rule( '^pay/(\d*)/?', 'index.php?var_1=1&var_2=$matches[1]', 'top' );
    }
}