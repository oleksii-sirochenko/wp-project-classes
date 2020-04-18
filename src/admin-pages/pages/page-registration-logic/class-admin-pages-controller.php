<?php


namespace your\space;

/**
 * manages logic related to registration and subordination of pages, tabs and menu settings
 */
class Admin_Pages_Controller {
	/**
	 * @var Admin_Page_Storage
	 */
	protected $page_storage;
	/**
	 * @var Admin_Page_Registrator
	 */
	protected $page_registrator;

	function __construct() {
		$this->page_storage     = new Admin_Page_Storage();
		$this->page_registrator = new Admin_Page_Registrator($this->page_storage);
	}

	function hooks() {
		$this->page_registrator->hooks();
	}

	function register_page( Registrable_Admin_Page $page, Registrable_Admin_Page $parent_page = null ) {
		$this->page_storage->add_page( $page, $parent_page );
	}
}