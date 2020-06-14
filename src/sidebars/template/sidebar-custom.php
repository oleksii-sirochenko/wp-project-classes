<?php
if ( ! is_active_sidebar( $sidebar_id ) ) {
    return;
}
?>
<ul class="sidebar sidebar_<?php echo $sidebar_id; ?>">
    <?php dynamic_sidebar( $sidebar_id ); ?>
</ul>
