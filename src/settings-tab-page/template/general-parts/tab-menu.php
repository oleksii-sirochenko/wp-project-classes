<div class="nav-tab-wrapper">
	<?php foreach ( $tabs as $tab ) { ?>
    <a href="<?php echo $settings_page_url; ?>&tab=<?php echo $tab['tab']->get_page_slug(); ?>"
        class="nav-tab <?php
				if ( isset( $tab['active'] ) && ! empty( $tab['active'] ) ) {
					echo 'nav-tab-active';
				}
				?>"><?php echo $tab['tab']->get_page_title(); ?></a>
	<?php } ?>
</div>
<?php
if ( isset( $active_tab['sub_tabs'] ) && ! empty( $active_tab['sub_tabs'] ) ) { ?>
  <div class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab ) {
			if ( isset( $tab['active'] ) && ! empty( $tab['active'] ) ) {
				foreach ( $tab['sub_tabs'] as $sub_tab ) {
					?>
          <a href="<?php echo $settings_page_url; ?>&tab=<?php echo $sub_tab['tab']->get_page_slug(); ?>"
              class="nav-tab <?php
							if ( isset( $sub_tab['active'] ) && ! empty( $sub_tab['active'] ) ) {
								echo 'nav-tab-active';
							}
							?>"><?php echo $sub_tab['tab']->get_page_title(); ?></a>
					<?php
				}
			}
		} ?>
  </div>
<?php } ?>