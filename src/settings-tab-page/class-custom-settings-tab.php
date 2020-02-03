<?php


namespace your\space;

/**
 * singleton
 * @method static General_Settings_Tab inst
 */
class Custom_Settings_Tab extends Settings_Page_Tab {
	public $option_key = 'namespace_general_settings';
	public $page_slug = 'namespace-general-settings';

	protected function __construct() {
		$this->menu_title = _x( 'General settings', 'Admin: general page tab', 'domain' );
		$this->page_title = _x( 'General settings', 'Admin: general page tab', 'domain' );
	}

	function r_page_template() {
		$args = array(
			'option_key' => $this->option_key,
			'options'    => $this->get_options(),
		);

		Reg::inst()->tmpl->get_template( 'general-settings-tab.php', 'admin/settings-page', $args );
	}
}