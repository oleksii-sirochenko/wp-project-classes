<ul class="subsubsub widefat">
	<?php
	$i     = 0;
	$count = count( $menu_items );
	foreach ( $menu_items as $menu_item ) { ?>
    <li>
      <a href="<?php echo $menu_item['url']; ?>" class="<?php
			if ( $menu_item['active'] ) {
				echo 'current';
			}
			?>"><?php echo $menu_item['title']; ?></a>
			<?php if ( $i + 1 < $count ) {
				echo "&nbsp;|&nbsp";
			} ?>
    </li>
		<?php
		$i ++;
	} ?>
</ul>
