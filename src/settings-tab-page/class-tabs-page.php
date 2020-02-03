<?php


namespace your\space;

/**
 * singleton
 * @method static Tabs_Page inst
 */
class Tabs_Page extends Settings_Tabs_Page {
	protected $page_slug = 'site-pages-settings';

	function __construct() {
		parent::__construct();
		$this->page_title = __( 'Settings', 'domain' );
		$this->menu_title = __( 'Settings tabs', 'domain' );
	}

	function hooks() {
		parent::hooks();
		add_action( 'admin_menu', array( $this, 'create_page' ), 11 );
	}

	protected function set_tabs() {
		$this->tabs = array(
			Custom_Settings_Tab::inst(),
		);

		$this->set_tab_settings_page_url();
	}

	/**
	 * enhanced page form can provide many tabs with content within settings page
	 */
	function r_page_template() {
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		$args = array(
			'tab_menu'   => $this->tab_menu,
			'active_tab' => $this->tab_menu->get_active_tab(),
		);

		Reg::inst()->tmpl->get_template( 'settings-page-tabs-form.php', 'admin/settings-pages/general-parts', $args );
	}


	protected function get_page_url_additional_args() {
		return array();
	}
}