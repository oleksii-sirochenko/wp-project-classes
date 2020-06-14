<div class="settings-page-wrap <?php echo $page_slug; ?>-wrap">
    <?php
    if ( isset( $menu_tmpl ) && ! empty( $menu_tmpl ) ) {
        echo $menu_tmpl;
        echo '<br>';
    }
    ?>
    <form action="options.php" method="post" enctype="multipart/form-data">
        <?php
        echo $settings_fields_tmpl;
        echo $page_tmpl;
        ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php echo $save_btn_label; ?>"/>
        </p>
    </form>
</div>