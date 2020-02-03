<?php
/**
 * @var $tab_menu your\space\Tabs_Menu
 * @var $active_tab your\space\Settings_Page_Tab
 */
?>
<div class="wrap">
  <form action="options.php" method="post" enctype="multipart/form-data">
		<?php
		$tab_menu->r_tab_menu();
		settings_fields( $active_tab->get_option_key() );
		$active_tab->r_page_template(); ?>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'domain' ) ?>"/>
    </p>
  </form>
</div>