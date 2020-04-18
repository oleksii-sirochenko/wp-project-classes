<div class="nav-tab-wrapper">
	<?php foreach ( $menu_items as $menu_item ) { ?>
    <a href="<?php echo $menu_item['url']; ?>" class="nav-tab <?php
		if ( $menu_item['active'] ) {
			echo 'nav-tab-active';
		}
		?>"><?php echo $menu_item['title']; ?></a>
	<?php } ?>
</div>