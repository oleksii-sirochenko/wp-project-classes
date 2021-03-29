<?php


namespace your\space;


class Admin_Page_Menu {
	/**
	 * @var Registrable_Admin_Page
	 */
	protected $page;
	/**
	 * @var Admin_Page_Storage
	 */
	protected $page_storage;
	
	function __construct( Registrable_Admin_Page $page, Admin_Page_Storage $page_storage ) {
		$this->page         = $page;
		$this->page_storage = $page_storage;
	}
	
	/**
	 * @return string html template for a page menu
	 */
	function get_tmpl() {
		$parent_page = $this->page_storage->get_parent_page( $this->page );
		$child_pages = $this->page_storage->get_child_pages( $this->page );
		
		if ( empty( $parent_page ) && empty( $child_pages ) ) {
			return '';
		}
		
		$top_parent_page = $this->page_storage->get_top_most_parent_page( $this->page );
		
		if ( is_null( $top_parent_page ) ) {
			$top_parent_page = $this->page;
		}
		
		return $this->get_tab_menu_tmpl( $top_parent_page );
	}
	
	protected function get_tab_menu_tmpl( Registrable_Admin_Page $parent_page ) {
		$args = array(
			'menu_items' => array(
				array(
					'url'    => esc_attr( $parent_page->get_page_url() ),
					'title'  => $parent_page->get_page_title(),
					'active' => $parent_page->is_current_page(),
				)
			)
		);
		
		$current_page_in_branch = null;
		/**
		 * @var Registrable_Admin_Page $page
		 */
		foreach ( $this->page_storage->get_child_pages( $parent_page ) as $page ) {
			$active = false;
			if ( $this->is_current_pages_branch( $page ) ) {
				$active                 = true;
				$current_page_in_branch = $page;
			}
			$args['menu_items'][] = array(
				'url'    => esc_attr( $page->get_page_url() ),
				'title'  => $page->get_page_title(),
				'active' => $active,
			);
		}
		
		$menu_tmpl = Reg::inst()->tmpl->get_template_as_string( 'tab-menu.php', 'admin/pages/general-parts', $args );
		
		if ( ! is_null( $current_page_in_branch ) ) {
			$menu_tmpl .= $this->get_sub_tab_menu_tmpl( $this->page_storage->get_child_pages( $current_page_in_branch ) );
		}
		
		return $menu_tmpl;
	}
	
	protected function is_current_pages_branch( Registrable_Admin_Page $page ) {
		if ( $page->is_current_page() ) {
			return true;
		} else {
			foreach ( $this->page_storage->get_child_pages( $page ) as $child_page ) {
				if ( $this->is_current_pages_branch( $child_page ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * @param Registrable_Admin_Page[] $pages
	 *
	 * @return string
	 */
	protected function get_sub_tab_menu_tmpl( array $pages ) {
		$args = array(
			'menu_items' => array(),
		);
		
		foreach ( $pages as $page ) {
			$args['menu_items'][] = array(
				'url'    => esc_attr( $page->get_page_url() ),
				'title'  => $page->get_page_title(),
				'active' => $page->is_current_page(),
			);
		}
		
		return Reg::inst()->tmpl->get_template_as_string( 'sub-tab-menu.php', 'admin/pages/general-parts', $args );
	}
}