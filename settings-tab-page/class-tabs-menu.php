<?php


namespace your\space;

/**
 * manages tabs menu for settings pages in the admin panel
 */
class Tabs_Menu {
	protected $tabs = array();
	protected $page_url;
	protected $tabs_marked = false;

	function __construct( $page_url ) {
		$this->page_url = $page_url;
	}

	function set_tab( Settings_Page_Tab $tab ) {
		$this->tabs[] = array(
			'tab'      => $tab,
			'sub_tabs' => array(),
		);
	}

	function set_sub_tabs( $tab_slug, $sub_tabs ) {
		if ( empty( $tab_slug ) || empty( $sub_tabs ) ) {
			return;
		}
		foreach ( $sub_tabs as $sub_tab ) {
			$this->set_sub_tab( $tab_slug, $sub_tab );
		}
	}

	function set_sub_tab( $tab_slug, Settings_Page_Tab $sub_tab ) {
		if ( empty( $tab_slug ) ) {
			return;
		}

		foreach ( $this->tabs as $key => $tab ) {
			if ( $tab['tab']->get_page_slug() === $tab_slug ) {
				$this->tabs[ $key ]['sub_tabs'][] = array( 'tab' => $sub_tab );
				break;
			}
		}
	}

	function r_tab_menu() {
		if ( empty( $this->tabs ) ) {
			return;
		}

		$this->mark_active_tabs();

		$args = array(
			'settings_page_url' => $this->page_url,
			'tabs'              => $this->tabs,
		);

		foreach ( $this->tabs as $tab ) {
			if ( isset( $tab['active'] ) && ! empty( $tab['active'] ) ) {
				$args['active_tab'] = $tab;
			}
		}

		Reg::inst()->tmpl->get_template( 'tab-menu.php', 'admin/settings-page/general-parts', $args );
	}

	protected function mark_active_tabs() {
		if ( empty( $this->tabs ) || $this->tabs_marked ) {
			return;
		}

		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			$active_tab_slug = $_GET['tab'];
		} else {
			$active_tab_slug = $this->tabs[0]['tab']->get_page_slug();
		}

		foreach ( $this->tabs as $key => $tab ) {
			if ( $tab['tab']->get_page_slug() === $active_tab_slug ) {
				$this->tabs[ $key ]['active'] = true;
				break;
			}
			if ( isset( $tab['sub_tabs'] ) && ! empty( $tab['sub_tabs'] ) ) {
				foreach ( $tab['sub_tabs'] as $sub_key => $sub_tab ) {
					if ( $sub_tab['tab']->get_page_slug() === $active_tab_slug ) {
						$this->tabs[ $key ]['active']                         = true;
						$this->tabs[ $key ]['sub_tabs'][ $sub_key ]['active'] = true;
					}
				}
			}
		}
		$this->tabs_marked = true;
	}

	/**
	 * @return Settings_Page_Tab | boolean
	 */
	function get_active_tab() {
		$this->mark_active_tabs();
		foreach ( $this->tabs as $tab ) {
			if ( isset( $tab['active'] ) && ! empty( $tab['active'] ) ) {
				if ( ! isset( $tab['sub_tabs'] ) || empty( $tab['sub_tabs'] ) ) {
					return $tab['tab'];
				} else {
					foreach ( $tab['sub_tabs'] as $sub_tab ) {
						if ( isset( $sub_tab['active'] ) && ! empty( $sub_tab['active'] ) ) {
							return $sub_tab['tab'];
						}
					}

					return $tab['tab'];
				}
			}
		}
	}
}