<?php

namespace your\space;
/**
 * abstract class that helps in easy registering an admin settings page. Covers general methods and sets required
 * properties with examples values
 */
abstract class Admin_Settings_Page implements Registrable_Admin_Page {
    /**
     * @var string Has to be unique among other pages and options in general.
     */
    protected $option_key = 'option_key';

    /**
     * @var string Has to be unique among other settings pages.
     */
    protected $page_slug = 'page_slug';

    /**
     * @var string Parent page slug is used to identify parent page by it's slug when it registers by add_submenu_page
     *             function.
     */
    protected $parent_page_slug = '';

    /**
     * @var string Shows on top of the page.
     */
    protected $page_title = 'page title';

    /**
     * @var string Shows in admin menu.
     */
    protected $menu_title = 'menu_title';

    /**
     * @var string Required user capability to edit this page.
     */
    protected $capability = 'manage_options';

    /**
     * @var string Menu icon in admin menu.
     */
    protected $icon_url = 'dashicons-admin-settings';

    /**
     * @var int|null page Position in admin menu.
     */
    protected $position = null;

    function sanitize_form_data( array $data ) {
        return $data;
    }

    function get_option_key() {
        return $this->option_key;
    }

    function get_options() {
        return get_option( $this->option_key, array() );
    }

    function set_options( $options ) {
        return update_option( $this->option_key, $options );
    }

	function get_options_item( $property, $default = null ) {
		$options = $this->get_options();

		if ( is_array( $options ) &&
		     ! empty( $options ) &&
		     isset( $options[ $property ] ) &&
		     ! empty( $options[ $property ] )
		) {
			return $options[ $property ];
		}

		if ( $default === null ) {
			$default = $this->get_default_options( $property );
		}

		return $default;
	}

	/**
	 * Returns default value for property. Should be overridden in an inherited class to provide instance specific
	 * defaults.
	 *
	 * @param $property
	 *
	 * @return mixed
	 */
	protected function get_default_options($property){
		return null;
	}

    function set_option_item( $key, $value ) {
        $options = $this->get_options();

        if ( is_array( $options ) ) {
            $options[ $key ] = $value;
        } else {
            $options = array( $key => $value );
        }

        return $this->set_options( $options );
    }

    function get_page_slug() {
        return $this->page_slug;
    }

    function get_parent_page_slug() {
        return $this->parent_page_slug;
    }

    function get_menu_title() {
        return $this->menu_title;
    }

    function get_page_title() {
        return $this->page_title;
    }

    function get_capability() {
        return $this->capability;
    }

    function get_icon_url() {
        return $this->icon_url;
    }

    function get_position() {
        return $this->position;
    }

    function is_current_page() {
        return strpos( home_url() . $_SERVER['REQUEST_URI'], $this->get_page_url() ) !== false;
    }

    function get_page_url() {
        $args      = array(
            'page' => $this->page_slug,
        );
        $url_query = build_query( $args );
        $url       = admin_url() . 'admin.php?' . $url_query;

        return $url;
    }
}