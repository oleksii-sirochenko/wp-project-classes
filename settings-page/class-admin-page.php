<?php


namespace your\space;

/**
 * singleton
 * class that helps create the settings page for Wordpress
 * @method static Admin_Page inst
 */
class Admin_Page extends Settings_Page {
	protected $option_key = 'your_option_name';

	/**
	 * page slug for settings page
	 * has to be unique for every page
	 * @var string
	 */
	protected $page_slug = 'your_options_page_slug';

	protected function __construct() {
		$this->page_title = __( 'Page_title', 'domain' );
		$this->menu_title = __( 'Menu_title', 'domain' );
	}

	/**
	 * simple page form that provides only 1 page without tabs of content
	 */
	function r_page_template() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$args = array(
			'option_key' => $this->option_key,
			'options'    => $this->get_options(),
		);
		Reg::inst()->tmpl->get_template( 'admin-page-template.php', 'admin/settings-pages', $args );
	}

	function sanitize_page( $data ) {

		//validate input or prepare to save in db
		if ( isset( $data['data'] ) && ! empty( $data['data'] ) ) {
			//...
		}

		return $data;
	}
}