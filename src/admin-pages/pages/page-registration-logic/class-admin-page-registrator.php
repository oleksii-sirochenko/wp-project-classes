<?php


namespace your\space;


class Admin_Page_Registrator {
	/**
	 * @var Admin_Page_Storage
	 */
	protected $page_storage;
	/**
	 * @var Admin_Page_Renderer;
	 */
	protected $admin_page_renderer;

	function __construct( Admin_Page_Storage $page_storage ) {
		$this->page_storage        = $page_storage;
		$this->admin_page_renderer = new Admin_Page_Renderer( $this->page_storage );
	}

	function hooks() {
		add_action( 'admin_menu', array( $this, 'register_pages' ), 11 );
		add_action( 'admin_init', array( $this, 'register_fields' ) );
	}

	function register_pages() {
		foreach ( $this->page_storage->get_top_level_pages() as $page ) {
			$this->register_top_level_page( $page );
			$this->register_child_pages( $this->page_storage->get_child_pages( $page ) );
		}
	}

	protected function register_child_pages( array $child_pages ) {
		foreach ( $child_pages as $child_page ) {
			$this->register_child_level_page( $child_page );
			$this->register_child_pages( $this->page_storage->get_child_pages( $child_page ) );
		}
	}

	protected function register_top_level_page( Registrable_Admin_Page $page ) {
		add_menu_page(
			$page->get_page_title(),
			$page->get_menu_title(),
			$page->get_capability(),
			$page->get_page_slug(),
			$this->admin_page_renderer->get_renderer( $page ),
			$page->get_icon_url(),
			$page->get_position()
		);
	}

	protected function register_child_level_page( Registrable_Admin_Page $page ) {
		add_submenu_page(
			null,
			$page->get_page_title(),
			$page->get_menu_title(),
			$page->get_capability(),
			$page->get_page_slug(),
			$this->admin_page_renderer->get_renderer( $page ),
			$page->get_position()
		);
	}

	function register_fields() {
		foreach ( $this->page_storage->get_pages() as $page ) {
			if ( ! current_user_can( $page->get_capability() ) ) {
				continue;
			}
			register_setting( $page->get_option_key(), $page->get_option_key(), array( $page, 'sanitize_form_data' ) );
		}
	}
}