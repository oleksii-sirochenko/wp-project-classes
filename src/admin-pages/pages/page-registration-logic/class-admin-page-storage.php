<?php


namespace your\space;


class Admin_Page_Storage {
	/**
	 * @var array key - slug, value Registrable_Admin_Page
	 */
	protected $page_storage = array();
	/**
	 * @var array key - parent page slug, value array of child page slugs
	 */
	protected $parent_relations = array();
	
	function __construct() {
	
	}
	
	function add_page( Registrable_Admin_Page $page, Registrable_Admin_Page $parent_page = null ) {
		$this->page_storage[ $page->get_page_slug() ] = $page;
		
		if ( ! is_null( $parent_page ) ) {
			if ( isset( $this->parent_relations[ $parent_page->get_page_slug() ] ) &&
			     is_array( $this->parent_relations[ $parent_page->get_page_slug() ] ) ) {
				if ( ! in_array( $page->get_page_slug(), $this->parent_relations[ $parent_page->get_page_slug() ] ) ) {
					$this->parent_relations[ $parent_page->get_page_slug() ][] = $page->get_page_slug();
				}
			} else {
				$this->parent_relations[ $parent_page->get_page_slug() ][] = $page->get_page_slug();
			}
		}
	}
	
	/**
	 * @param string $page_slug
	 *
	 * @return Registrable_Admin_Page|null
	 */
	function get_page( $page_slug ) {
		if ( isset( $this->page_storage[ $page_slug ] ) ) {
			return $this->page_storage[ $page_slug ];
		}
		
		return null;
	}
	
	/**
	 * @param Registrable_Admin_Page $parent_page
	 *
	 * @return Registrable_Admin_Page[]
	 */
	function get_child_pages( Registrable_Admin_Page $parent_page ) {
		if ( isset( $this->parent_relations[ $parent_page->get_page_slug() ] ) &&
		     ! empty( $this->parent_relations[ $parent_page->get_page_slug() ] ) ) {
			$pages = array();
			foreach ( $this->parent_relations[ $parent_page->get_page_slug() ] as $child_page_slug ) {
				if ( isset( $this->page_storage[ $child_page_slug ] ) &&
				     ! empty( $this->page_storage[ $child_page_slug ] ) ) {
					$pages[] = $this->page_storage[ $child_page_slug ];
				}
			}
			
			return $pages;
		}
		
		return array();
	}
	
	/**
	 * @param Registrable_Admin_Page $page
	 *
	 * @return Registrable_Admin_Page|null
	 */
	function get_parent_page( Registrable_Admin_Page $page ) {
		foreach ( $this->parent_relations as $parent_slug => $child_pages_slugs ) {
			if ( in_array( $page->get_page_slug(), $child_pages_slugs ) ) {
				return $this->page_storage[ $parent_slug ];
			}
		}
		
		return null;
	}
	
	function get_top_most_parent_page( Registrable_Admin_Page $page ) {
		$parent_page = $this->get_parent_page( $page );
		
		if ( ! is_null( $parent_page ) ) {
			$current_parent_page = $this->get_top_most_parent_page( $parent_page );
			if ( ! is_null( $current_parent_page ) ) {
				return $current_parent_page;
			}
		}
		
		return $parent_page;
	}
	
	/**
	 * @return Registrable_Admin_Page[]
	 */
	function get_pages() {
		return array_values( $this->page_storage );
	}
	
	/**
	 * @return Registrable_Admin_Page[]
	 */
	function get_top_level_pages() {
		$pages = array();
		
		foreach ( $this->page_storage as $page ) {
			if ( empty( $this->get_parent_page( $page ) ) ) {
				$pages[] = $page;
			}
		}
		
		return $pages;
	}
}