<?php


namespace your\space;

class Admin_Page_3 extends Admin_Settings_Page {
	protected $option_key = 'admin_page_3';

	/**
	 * page slug for settings page
	 * has to be unique for every page
	 * @var string
	 */
	protected $page_slug = 'admin_page_3';

	function __construct() {
		$this->page_title = __( 'Admin page 3', 'domain' );
		$this->menu_title = __( 'Admin page 3', 'domain' );
	}

	function r_tmpl() {
		$args = array(
			'option_key' => $this->option_key,
			'options'    => $this->get_options(),
			'page_title' => $this->page_title,
		);

		Reg::inst()->tmpl->get_template( 'admin-page-3.php', 'admin/pages', $args );
	}

	function sanitize_form_data( array $data ) {
		//validate input or prepare to save in db
		if ( isset( $data['data'] ) && ! empty( $data['data'] ) ) {
			//...
		}

		return $data;
	}
}