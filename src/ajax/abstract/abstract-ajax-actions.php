<?php


namespace your\space;


abstract class AJAX_Actions {
    function get_actions() {
        $actions_sets = array();
        
        foreach ( $this->get_actions_sets_array() as $actions_set ) {
            $actions_sets[] = $actions_set;
        }
        
        $actions = array();
        foreach ( $actions_sets as $actions_set ) {
            $actions[] = $actions_set;
        }
        
        return $actions;
    }
    
    /**
     * should return array of actions set
     *
     * @return array
     */
    abstract protected function get_actions_sets_array();
    
    /**
     * should return associative array of scripts data
     *
     * @return array
     */
    function get_scripts_data() {
        return null;
    }
}