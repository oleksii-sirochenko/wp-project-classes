<?php

namespace your\space;

/**
 * Controls custom rewrite rules. Should be a central place where all rewrite logic works.
 */
class Rewrite_Rules {
    function __construct() {
    
    }
    
    /**
     * Attaches methods to hooks.
     */
    function hooks() {
        add_action( 'init', array( $this, 'add_rewrite_rules' ), 10, 0 );
    }
    
    /**
     * Adds custom rewrite rules.
     */
    function add_rewrite_rules() {
        $this->page_rule();
    }
    
    /**
     * Custom rewrite rule example.
     */
    protected function page_rule() {
        add_rewrite_tag( '%var_1%', '([^&]+)' );
        add_rewrite_tag( '%var_2%', '([^&]+)' );
        add_rewrite_rule( '^pay/(\d*)/?', 'index.php?var_1=1&var_2=$matches[1]', 'top' );
    }
}