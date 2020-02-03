<?php

namespace your\space;

abstract class Settings_Page_Tab extends Settings_Page {
	/**
	 * @var Settings_Page_Tab[] $sections
	 */
	protected $sub_tabs = array();
	protected $settings_page_url;

	protected function __construct() {
		$this->set_sub_tabs();
	}

	function is_current_page() {
		return isset( $_GET['tab'] ) && $_GET['tab'] == $this->page_slug;
	}

	function hooks() {
		add_action( 'admin_init', array( $this, 'register_fields' ) );
		$this->init_sub_tabs_hooks();
	}

	function get_sub_tabs() {
		return $this->sub_tabs;
	}

	protected function set_sub_tabs() {
		$this->sub_tabs = array();
	}

	protected function init_sub_tabs_hooks() {
		if ( is_array( $this->sub_tabs ) && ! empty( $this->sub_tabs ) ) {
			foreach ( $this->sub_tabs as $sub_tab ) {
				if ( method_exists( $sub_tab, 'hooks' ) ) {
					$sub_tab->hooks();
				}
			}
		}
	}

	function set_tab_settings_page_url( $settings_page_url ) {
		$this->settings_page_url = $settings_page_url;
		$this->set_sub_tabs_settings_page_url();
	}

	function get_settings_page_url() {
		return $this->settings_page_url;
	}

	function set_sub_tabs_settings_page_url() {
		foreach ( $this->sub_tabs as $sub_tab ) {
			$sub_tab->set_tab_settings_page_url( $this->settings_page_url );
		}
	}

	function get_tab_url() {
		return add_query_arg( array(
			'tab' => $this->get_page_slug(),
		), $this->settings_page_url );
	}
}