<?php


namespace your\space;


class Admin_Page_Renderer {
	protected $page_storage;
	
	function __construct( Admin_Page_Storage $page_storage ) {
		$this->page_storage = $page_storage;
	}
	
	/**
	 * @param Registrable_Admin_Page $page
	 *
	 * @return \Closure
	 */
	function get_renderer( Registrable_Admin_Page $page ) {
		$page_menu = new Admin_Page_Menu( $page, $this->page_storage );
		$args      = array(
			'page_slug'            => $page->get_page_slug(),
			'menu_tmpl'            => $page_menu->get_tmpl(),
			'settings_fields_tmpl' => $this->get_settings_fields_tmpl( $page ),
			'page_tmpl'            => $this->get_page_tmpl( $page ),
			'save_btn_label'       => esc_attr( 'Save Changes', 'domain' ),
		);
		
		return function () use ( $args ) {
			Reg::inst()->tmpl->get_template( 'page.php', 'admin/pages/general-parts', $args );
		};
	}
	
	protected function get_page_tmpl( Registrable_Admin_Page $page ) {
		ob_start();
		$page->r_tmpl();
		
		return ob_get_clean();
	}
	
	protected function get_settings_fields_tmpl( Registrable_Admin_Page $page ) {
		ob_start();
		settings_fields( $page->get_option_key() );
		
		return ob_get_clean();
	}
}