<?php


namespace your\space;


abstract class Settings_Tabs_Page extends Settings_Page {
	/**
	 * @var Settings_Page_Tab[]
	 */
	protected $tabs = array();
	/**
	 * @var Tabs_Menu
	 */
	protected $tab_menu;

	protected function __construct() {
		$this->set_tabs();
		$this->set_tab_menu();
	}

	abstract protected function set_tabs();

	function hooks() {
		$this->init_tabs_hooks();
	}

	protected function set_tab_menu() {
		$this->tab_menu = new Tabs_Menu( $this->get_page_url() );
		foreach ( $this->tabs as $tab ) {
			$this->tab_menu->set_tab( $tab );
			$this->tab_menu->set_sub_tabs( $tab->get_page_slug(), $tab->get_sub_tabs() );
		}
	}

	protected function set_tab_settings_page_url() {
		foreach ( $this->tabs as $tab ) {
			$tab->set_tab_settings_page_url( $this->get_page_url() );
		}
	}

	protected function init_tabs_hooks() {
		foreach ( $this->tabs as $tab ) {
			if ( method_exists( $tab, 'hooks' ) ) {
				$tab->hooks();
			}
		}
	}

	function get_page_url() {
		$args = array(
			'page' => $this->page_slug,
		);
		$args += $this->get_page_url_additional_args();

		return add_query_arg( $args, admin_url() . 'edit.php' );
	}

	protected function get_page_url_additional_args() {
		return array();
	}
}